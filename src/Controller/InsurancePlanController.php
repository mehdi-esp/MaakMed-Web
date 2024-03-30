<?php

namespace App\Controller;

use App\Entity\InsurancePlan;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InsurancePlanController extends AbstractController
{
    #[Route('/insurance/plan', name: 'app_insurance_plan')]
    public function index(): Response
    {
        return $this->render('insurance_plan/index.html.twig', [
            'controller_name' => 'InsurancePlanController',
        ]);
    }
    #[Route('/insurance/plan/ListPlans', name: 'app_insurance_plan_ListPlans')]
    public function listPlansPatient(EntityManagerInterface $entityManager):Response
    {
        $plans = $entityManager->getRepository(InsurancePlan::class)->findAll();
        return $this->render('insurance_plan/ListPlansPatient.html.twig', [
            'plans' => $plans,
        ]);
    }

}
