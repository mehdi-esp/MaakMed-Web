<?php

namespace App\Controller;

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
}

