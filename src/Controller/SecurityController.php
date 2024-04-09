<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
       /* // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Check Cloudflare Turnstile before allowing login
        $zoneId = 'lp5UVAWcW8eh4PxIDknmpn8JPCa82LkFfqmxi40D'; // Replace 'YOUR_ZONE_ID' with your Cloudflare Zone ID
        $email = $request->request->get('_username');
        $password = $request->request->get('_password');

        // Call Cloudflare service to check Turnstile
        $turnstileResponse = $cloudflareService->checkTurnstile($zoneId, $email, $password);

        // Handle Turnstile response
        if ($turnstileResponse->passes()) {
            // Allow user to log in
            return $this->render('security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        } else {
            // Handle Turnstile failure, you can redirect or show an error message
            return $this->render('security/turnstile_failure.html.twig');
        }*/
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route('/password-change', name: 'password_change')]
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // Create a form
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the current user
            $user = $this->getUser();

            // Encode the new password
            $newPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('new_password')->getData()
            );

            // Set the new password
            $user->setPassword($newPassword);

            // Save the user
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect to the login page
            return $this->redirectToRoute('app_login');
        }
        $response = new Response(
            status: $form->isSubmitted()
                ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK,
        );
        // Render the form
        return $this->render('security/password_change.html.twig', [
            'form' => $form->createView()], $response
        );
    }
}


