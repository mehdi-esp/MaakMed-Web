<?php

namespace App\Controller;

use App\Form\Profile\AdminP;
use App\Form\Profile\DoctorP;
use App\Form\Profile\PatientP;
use App\Form\Profile\PharmacyP;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\RegularUser;
use App\Entity\HumanUser;
use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Pharmacy;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class ProfileController extends AbstractController
{


    public function __construct(private readonly MailerInterface $mailer, private readonly EmailVerifier $emailVerifier, private readonly Breadcrumbs $breadcrumbs,)
    {

    }

    #[Route("/profile", name: "profile")]
    #[IsGranted("ROLE_USER")]
    public function profile()
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $this->breadcrumbs->addItem("My Profile");
        return $this->render('user/profile.html.twig', ['user' => $currentUser]);
    }


    #[Route("/profile/edit", name: "profile_edit", methods: ["GET", "POST"])]
    public function editProfile(Request $request, EntityManagerInterface $entityManager)
    {
        $this->breadcrumbs->addRouteItem("My Profile", "profile");
        $this->breadcrumbs->addRouteItem("Edit", "profile_edit");
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }

        // Use the appropriate form depending on the type of user
        if ($currentUser instanceof Doctor) {
            $form = $this->createForm(DoctorP::class, $currentUser);
        } elseif ($currentUser instanceof Patient) {
            $form = $this->createForm(PatientP::class, $currentUser);
        } elseif ($currentUser instanceof Pharmacy) {
            $form = $this->createForm(PharmacyP::class, $currentUser);
        } elseif ($currentUser instanceof Admin) {
            $form = $this->createForm(AdminP::class, $currentUser);
        } else {
            throw new \Exception('Unknown user type');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('profile');
        }


        $response = new Response(
            status: $form->isSubmitted()
                ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK,
        );


        return $this->render('user/profile_edit.html.twig', [
            'user' => $currentUser,
            'form' => $form->createView(),
        ], $response);
    }


    // TODO: Maybe add a specific voter

    #[Route("/profile/{username}", name: "user_profile")]
    #[IsGranted("ROLE_USER")]
    public function userProfile(User $user): Response
    {
        if ($this->getUser() instanceof Admin) {
            $this->breadcrumbs->addRouteItem("Users", "app_user");
        }
        $this->breadcrumbs->addItem("Profile");
        $this->breadcrumbs->addItem($user->getUsername(), "user_profile", ['username' => $user->getUsername()]);
        if ($user === $this->getUser()) {
            return $this->redirectToRoute("profile");
        }
        return $this->render('user/profile.html.twig', ['user' => $user]);
    }


    #[Route('/verify/email', name: 'app_verify_email', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function verifyUserEmail(VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
        // deny access for admin
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // Get the logged-in user
        /** @var Doctor|Patient|Pharmacy $user */
        $user = $this->getUser();

        // If the user is already verified, redirect to the profile page
        if ($user->getIsVerified()) {
            $this->addFlash('info', 'Your email is already verified.');
            return $this->redirectToRoute('profile');
        }
        // Generate the email verification link
        $signatureComponents = $verifyEmailHelper->generateSignature(
            'app_confirm_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        // Get the signed URL from the signature components
        $signedUrl = $signatureComponents->getSignedUrl();

        // Pass the signed URL to the sendEmailConfirmation method
        $this->sendEmailConfirmation($signedUrl);

        // Redirect to the profile page with a success message
        $this->addFlash('success', 'A verification email has been sent to your email address.');
        return $this->redirectToRoute('profile');
    }

    private function sendEmailConfirmation(string $signedUrl): void
    {
        $address = $this->getUser()->getEmail();
        $email = (new TemplatedEmail())
            ->from(new Address('***REMOVED***', 'Email Verification'))
            ->to($address)
            ->subject('Please Confirm your Email')
            ->htmlTemplate('registration/confirmation_email.html.twig')
            ->context([
                'signedUrl' => $signedUrl,
            ]);

        $this->mailer->send($email);
    }

    #[Route('/confirm/email', name: 'app_confirm_email')]
    public function confirmEmail(
        Request                    $request,
        VerifyEmailHelperInterface $verifyEmailHelper,
        UserRepository             $userRepository,
        EntityManagerInterface     $entityManager
    ): Response
    {
        // Get the user by the 'id' parameter in the URL
        $user = $userRepository->find($request->query->get('id'));

        // If the user does not exist, throw a 404 error
        if (!$user) {
            throw $this->createNotFoundException();
        }

        // Validate the email confirmation link
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );
        } catch (VerifyEmailExceptionInterface $e) {
            // If the link is invalid, redirect to the profile page with an error message
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('profile');
        }

        // If the link is valid, mark the user as verified and save the changes
        $user->setIsVerified(true);
        $entityManager->flush();

        // Redirect to the profile page with a success message
        $this->addFlash('success', 'Your email has been verified.');
        return $this->redirectToRoute('profile');
    }

    #[Route('/account/delete', name: 'app_account_delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteAccount(Request                $request,
                                  EntityManagerInterface $entityManager,
                                  TexterInterface        $texter,
                             TokenStorageInterface $tokenStorage,
    ): Response
    {
        $sms = new SmsMessage(
        // the phone number to send the SMS message to
            '+21653905361',
            // the message
            'Deleted account!',
        );

        $sentMessage = $texter->send($sms);

        $entityManager->remove($this->getUser());
        $entityManager->flush();

        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        $this->addFlash('success', 'Your account has been deleted.');

        return $this->redirectToRoute('app_home');
    }

}
