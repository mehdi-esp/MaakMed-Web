<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicationController extends AbstractController
{
    #[Route('/medication', name: 'app_medication')]
    public function index(): Response
    {
        return $this->render('medication/index.html.twig', [
            'controller_name' => 'MedicationController',
        ]);
    }
}
