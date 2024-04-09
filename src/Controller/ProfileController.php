<?php

namespace App\Controller;

use App\Form\Profile\AdminP;
use App\Form\Profile\DoctorP;
use App\Form\Profile\PatientP;
use App\Form\Profile\PharmacyP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\HumanUser;
use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Pharmacy;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{

    #[Route("/profile", name: "profile")]
    public function profile()
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/profile.html.twig', ['user' => $currentUser]);
    }


    #[Route("/profile/edit", name: "profile_edit", methods: ["GET", "POST"])]
    public function editProfile(Request $request, EntityManagerInterface $entityManager)
    {
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
            $entityManager->persist($currentUser);
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

    #[Route("/profile/{id}", name: "user_profile")]
    public function userProfile(UserRepository $userRepository, $id): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        // Allow access if the current user is an admin
        $currentUser = $this->getUser();
        if (!$currentUser || (!$this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException('You do not have permission to view this page.');
        }

        return $this->render('user/profile.html.twig', ['user' => $user, 'app_user' => $currentUser]);
    }
}
