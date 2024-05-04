<?php



namespace App\Controller;

use App\Form\{PatientType, DoctorType, PharmacyType};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;


class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    #[Route('/register', name: 'app_register')]
    public function registerOther(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
    ): Response {
        $types = [
            'patient' => PatientType::class,
            'doctor' => DoctorType::class,
            'pharmacy' => PharmacyType::class,
        ];

        $forms = array_map(self::createForm(...), $types);

        if ($request->isMethod('POST')) {
            foreach ($forms as $form) {
                $form->handleRequest($request);

                if (!$form->isSubmitted()) {
                    continue;
                }

                if (!$form->isValid()) {
                    continue;
                }

                $user = $form->getData();
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_login');
            }
        }

        $views = array_map(fn (FormInterface $form) => $form->createView(), $forms);

        $response = new Response(
            status: array_filter($forms, fn ($form) => $form->isSubmitted())
                ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK,
        );

        return $this->render('registration/register.html.twig', ['forms' => $views], $response);
    }

}
