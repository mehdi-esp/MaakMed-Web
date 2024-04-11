<?php

namespace App\Controller;

use App\Entity\InsurancePlan;
use App\Entity\Patient;
use App\Entity\Subscription;
use App\Form\SubscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SubscriptionController extends AbstractController
{
    #[Route('/subscription', name: 'app_subscription')]
    public function index(): Response
    {
        return $this->render('subscription/index.html.twig', [
            'controller_name' => 'SubscriptionController',
        ]);
    }
    #[Route('/subscription/active', name: 'app_subscription_active')]
    #[IsGranted("ROLE_PATIENT")]
    public function getActivePlan(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof Patient) {
            throw new \Exception('Logged in user must be a Patient');
        }

        $activePlan = $entityManager->getRepository(Subscription::class)
            ->findOneBy([
                'patient' => $user,
                'status' => 'active'
            ]);

        $response = [
            'hasActivePlan' => $activePlan !== null
        ];

        if ($activePlan !== null) {
            $response['planName'] = $activePlan->getPlan()->getName();
        }

        return new JsonResponse($response);
    }
    /**
     * @throws \Exception
     */
   #[Route('/subscription/Subscribe/{planId}', name: 'app_subscription_add',methods: ['POST'])]
   #[IsGranted("ROLE_PATIENT")]
   public function Subscribe(EntityManagerInterface $entityManager, $planId): Response
   {
       $user = $this->getUser();
       if (!$user instanceof Patient) {
           throw new \Exception('Logged in user must be a Patient');
       }
       // Check if the patient already has a subscription with status "pending" or "active"
       $existingSubscription = $entityManager->getRepository(Subscription::class)
           ->findOneBy([
               'patient' => $user,
               'status' => ['pending', 'active']
           ]);
       if ($existingSubscription) {
           $this->addFlash('warning', 'You already have an active or pending subscription.');
           return $this->redirectToRoute('app_insurance_plan_ListPlans');
       }
       $subscription = new Subscription();
       $subscription->setPatient($user);
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
       $this->addFlash('message', 'Subscription created successfully.');
       $this->addFlash('status', 'success');
       return $this->redirectToRoute('app_insurance_plan_ListPlans');
   }

    #[Route('/subscription/ListSubscriptions', name: 'app_subscription_list', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function ListSubscriptionsAdmin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $status = $request->query->get('status');
        $planName = $request->query->get('planName');

        $queryBuilder = $entityManager->getRepository(Subscription::class)->createQueryBuilder('s')
            ->leftJoin('s.plan', 'p');

        if (!empty($status)) {
            $queryBuilder->where('s.status = :status')
                ->setParameter('status', $status);
        }

        if (!empty($planName)) {
            $queryBuilder->andWhere('p.name LIKE :planName')
                ->setParameter('planName', $planName . '%');
        }

        $subscriptions = $queryBuilder
            ->orderBy('s.status', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('subscription/ListSubscriptionsAdmin.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }
   #[Route('/subscription/search', name: 'app_subscription_search', methods: ['GET'])]
   #[IsGranted("ROLE_ADMIN")]
   public function search(Request $request, EntityManagerInterface $entityManager): Response
   {
       $planName = $request->query->get('planName');
       $status = $request->query->get('status');

       $queryBuilder = $entityManager->getRepository(Subscription::class)->createQueryBuilder('s')
           ->leftJoin('s.plan', 'p')
           ->where('p.name LIKE :planName')
           ->setParameter('planName', $planName . '%');
        if (!empty($status)) {
                $queryBuilder->andWhere('s.status = :status')
                    ->setParameter('status', $status);
            }
       $subscriptions = $queryBuilder
           ->orderBy('s.status', 'ASC')
           ->getQuery()
           ->getResult();

       $subscriptionsArray = array_map(function($subscription) {
           return [
               'id' => $subscription->getId(),
               'planName' => $subscription->getPlan()->getName(),
               'patientUsername' => $subscription->getPatient()->getUsername(),
               'startDate' => $subscription->getStartDate()->format('Y-m-d'),
               'endDate' => $subscription->getEndDate()->format('Y-m-d'),
               'status' => $subscription->getStatus(),
           ];
       }, $subscriptions);

       return new JsonResponse($subscriptionsArray);
   }

    #[Route('/subscription/UpdateSubscription/{id}', name: 'app_subscription_Update')]
    #[IsGranted("ROLE_ADMIN")]
    public function UpdateSub(Request $req, Subscription $Sub, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubscriptionType::class, $Sub);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_subscription_list', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );

        return $this->render('subscription/update.html.twig', [
            'Subscription' => $Sub,
            'form' => $form->createView(),
        ], $response);
    }
    #[Route('/subscription/DeleteSubscription/{id}', name: 'app_subscription_Delete')]
    #[IsGranted("ROLE_ADMIN")]
    public function CancelSub(Subscription $Sub, EntityManagerInterface $entityManager): Response
    {
        $Sub->setStatus('canceled');
        $entityManager->persist($Sub);
        $entityManager->flush();
        return $this->redirectToRoute('app_subscription_listAdmin');
    }
}
