<?php

namespace App\Controller;

use App\Entity\InsurancePlan;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    #[Route('/subscription', name: 'app_subscription')]
    public function index(): Response
    {
        return $this->render('subscriiption/index.html.twig', [
            'controller_name' => 'SubscriiptionController',
        ]);
    }
    #Route('/subscription/Subscribe/{planId}', name: 'app_subscription_add')
    #[IsGranted("ROLE_PATIENT")]
    public function Subscribe(EntityManagerInterface $entityManager, $planId): Response
    {
        $subscription = new Subscription();
        $Patient = $this->getUser();
        $subscription->setPatient($Patient);
        $currentDate = new \DateTimeImmutable();
        $subscription->setStartDate($currentDate);

        // Add one year to the current date
        $endDate = $currentDate->add(new \DateInterval('P1Y'));
        $subscription->setEndDate($endDate);
        $subscription->setStatus('pending');

        // Get the plan with the given ID and set it on the subscription
        $plan = $entityManager->getRepository(InsurancePlan::class)->find($planId);
        $subscription->setPlan($plan);

        $entityManager->persist($subscription);
        $entityManager->flush();

        return $this->redirectToRoute('app_subscription_index', [], Response::HTTP_SEE_OTHER);
    }
}
