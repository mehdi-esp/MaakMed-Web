<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/prescription')]
class PrescriptionController extends AbstractController
{
    #[Route('/', name: 'app_prescription_index', methods: ['GET'])]


    public function index(): Response
    {
        return $this->render('prescription/index.html.twig', [
            'controller_name' => 'PrescriptionController',
        ]);
    }
}
