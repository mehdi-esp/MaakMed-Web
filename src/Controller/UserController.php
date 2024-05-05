<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/list', name: 'app_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    #[Route('/user/{id}/ban', name: 'user_ban', methods: ["POST"])]
    #[IsGranted('ROLE_ADMIN')]
    public function ban(
        Request $request,
        EntityManagerInterface $entityManager,
        User $user,
    ): Response {
        if ($this->isCsrfTokenValid('ban' . $user->getId(), $request->request->get('_token'))) {
            $username = $user->getUsername();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('info', "User {$username} has been banned.");
        }

        return $this->redirectToRoute('app_user');
    }


}