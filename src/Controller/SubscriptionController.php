<?php

namespace App\Controller;

use App\Entity\InsurancePlan;
use App\Entity\Patient;
use App\Entity\Subscription;
use App\Form\SubscriptionType;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SubscriptionController extends AbstractController
{
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
    #[Route('/{planId<\d+>}/{amount}', name: 'app_subscription_Activate')]
    #[IsGranted("ROLE_PATIENT")]
    public function createCheckoutSession(int $planId, float $amount, EntityManagerInterface $entityManager): JsonResponse|RedirectResponse
    {
         $user = $this->getUser();
               if (!$user instanceof Patient) {
                   throw new \Exception('Logged in user must be a Patient');
               }

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
               $this->addFlash('success', 'Subscription created successfully.');
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_KEY']);
        header('Content-Type: application/json');
        // Get the plan with the given ID
        $plan = $entityManager->getRepository(InsurancePlan::class)->find($planId);
        if (!$plan) {
            throw $this->createNotFoundException('The plan does not exist');
        }

        $namePlan = $plan->getName();
        $amountInCents = $amount * 100;

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $amountInCents,
                        'product_data' => [
                            'name' => $namePlan,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'http://127.0.0.1:8000/subscription/success',
                'cancel_url' => 'http://127.0.0.1:8000/subscription/failure',
            ]);
            return new RedirectResponse($session->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log($e->getMessage());
            return 'An error occurred while creating the Stripe Checkout Session: ' . $e->getMessage();
        }
    }
    #[Route('/subscription/cancel/{planId}', name: 'app_subscription_Cancel')]
    #[IsGranted("ROLE_PATIENT")]
    public function cancelSubscription(int $planId, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Patient) {
            throw new \Exception('Logged in user must be a Patient');
        }

        $subscription = $entityManager->getRepository(Subscription::class)
            ->findOneBy([
                'patient' => $user,
                'plan' => $planId,
                'status' => ['active', 'pending']
            ]);

        if (!$subscription) {
            $this->addFlash('warning', 'No active or pending subscription found for this plan.');
            return $this->redirectToRoute('app_insurance_plan_ListPlans');
        }

        $subscription->setStatus('canceling');
        $entityManager->persist($subscription);
        $entityManager->flush();

        $this->addFlash('message', 'Subscription is being canceled.');
        $this->addFlash('status', 'success');

        return $this->redirectToRoute('app_insurance_plan_ListPlans');
    }

   #[Route('/subscription/success', name: 'app_subscription_success', methods: ['GET'])]
   #[IsGranted("ROLE_PATIENT")]
   public function paymentSuccess(): Response
   {
       return $this->render('subscription/success.html.twig');
   }
   #[Route('/subscription/failure', name: 'app_subscription_failure', methods: ['GET'])]
      #[IsGranted("ROLE_PATIENT")]
      public function paymentFailure(): Response
      {
          return $this->render('subscription/failure.html.twig');
      }
}
