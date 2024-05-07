<?php

namespace App\Controller;

use App\Entity\InsurancePlan;
use App\Entity\Patient;
use App\Entity\Subscription;
use App\Form\SubscriptionType;
use Stripe\Stripe;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
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
use Jungi\FrameworkExtraBundle\Attribute\RequestParam;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

#[Route('/subscription')]
class SubscriptionController extends AbstractController
{
     public function __construct(
            private readonly Breadcrumbs $breadcrumbs,
            private readonly MailerInterface $mailer,
        ) {
        }

   #[Route('/', name: 'app_subscription_list', methods: ['GET'])]
   #[IsGranted("ROLE_ADMIN")]
   public function ListSubscriptionsAdmin(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
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
               ->setParameter('planName', '%' . $planName . '%');
       }

       $queryBuilder->orderBy('s.status', 'ASC');
       $page = $request->query->getInt('page', 1);
       $limit = 5;
       $pagination = $paginator->paginate(
           $queryBuilder,
           $page,
           $limit
       );
        $this->breadcrumbs->addRouteItem("Subscription", "app_subscription_list");
       return $this->render('subscription/ListSubscriptionsAdmin.html.twig', [
           'pagination' => $pagination,
       ]);
   }
   #[Route('/search', name: 'app_subscription_search', methods: ['GET'])]
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

    #[Route('/{id}/update', name: 'app_subscription_Update', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function UpdateSub(Request $req, Subscription $Sub, EntityManagerInterface $entityManager): Response
    {
        $this->breadcrumbs->addRouteItem("Subscription", "app_subscription_list");
        $this->breadcrumbs->addRouteItem($Sub->getId(), "app_subscription_Update", ["id" => $Sub->getId()]);
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
    #[Route('/{id}/delete', name: 'app_subscription_Delete',methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function CancelSub(Subscription $Sub, EntityManagerInterface $entityManager): Response
    {
        $Sub->setStatus('canceled');
        $entityManager->persist($Sub);
        $entityManager->flush();
        return $this->redirectToRoute('app_subscription_list');
    }
    #[Route('/subscribe', name: 'app_subscription_Activate',methods: ['GET','POST'])]
    #[IsGranted("ROLE_PATIENT")]
    public function Subscribe(#[RequestParam] int $planId, #[RequestParam] float $amount, EntityManagerInterface $entityManager): JsonResponse|RedirectResponse
    {
         \Stripe\Stripe::setApiKey($_ENV['STRIPE_KEY']);
         $user = $this->getUser();
               if (!$user instanceof Patient) {
                   throw new \Exception('Logged in user must be a Patient');
               }
               try {
                       $plan = $entityManager->getRepository(InsurancePlan::class)->find($planId);
                       if (!$plan) {
                           throw $this->createNotFoundException('The plan does not exist');
                       }

               $existingSubscription = $entityManager->getRepository(Subscription::class)
                          ->findOneBy([
                              'patient' => $user,
                              'status' => ['active', 'canceling']
                          ]);
                      if ($existingSubscription) {
                          $this->addFlash('warning', 'You already have an active or a subscription in canceling status.');
//                           return $this->redirectToRoute('app_insurancePlan_list');
                          $jsonResponse = ['url' => $this->generateUrl('app_insurancePlan_list')];
                                                     return new JsonResponse($jsonResponse);

                      }
               $pendingSubscriber = $entityManager->getRepository(Subscription::class)
                           ->findOneBy([
                               'patient' => $user,
                               'status' => ['pending']
                           ]);
               if($pendingSubscriber){
               $this->addFlash('success', 'Plan Pending redirecting to payment session .');
                $session = $this->createCheckoutSession($plan->getName(), $amount);
                $jsonResponse = ['url' => $session->url];
                           return new JsonResponse($jsonResponse);
               }
                $session = $this->createCheckoutSession($plan->getName(), $amount);
                $jsonResponse = ['url' => $session->url];

               $subscription = new Subscription();
               $subscription->setPatient($user);
               $currentDate = new \DateTimeImmutable();
               $subscription->setStartDate($currentDate);
               $endDate = $currentDate->add(new \DateInterval('P1Y'));
               $subscription->setEndDate($endDate);
               $subscription->setStatus('pending');
               $plan = $entityManager->getRepository(InsurancePlan::class)->find($planId);
               $subscription->setPlan($plan);
                $entityManager->persist($subscription);
                $entityManager->flush();
           return new JsonResponse($jsonResponse);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log($e->getMessage());
            throw new \Exception('An error occurred while creating the Stripe Checkout Session: ' . $e->getMessage());
        }
    }

    public function createCheckoutSession(string $namePlan, float $amount) : \Stripe\Checkout\Session
    {
    \Stripe\Stripe::setApiKey($_ENV['STRIPE_KEY']);
        $amountInCents = $amount * 100;

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
            'success_url' => $this->generateUrl('app_subscription_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_subscription_failure', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        $jsonResponse = ['url' => $session->url];
        return $session;
    }

    #[Route('/webhook',name: 'app_subscription_webhook', methods: ['POST'])]
    public function handleWebhook(Request $request,EntityManagerInterface $entityManager): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $endpoint_secret = $_ENV['ENDPOINT_SECRET'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpoint_secret
            );
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new JsonResponse(['error' => 'Bad Request'], Response::HTTP_BAD_REQUEST);
        }
        if ($event->type === 'checkout.session.completed') {
            $data = $event->data->object;
            $email = $data->customer_details->email;

            $this->sendEmailConfirmation($email);
            $userRepository = $entityManager->getRepository(\App\Entity\User::class);
            $userId = $userRepository->findOneByEmail($email)?->getId();

            if (!$userId) {
         
                return new JsonResponse(['error' => 'Patient not found'], Response::HTTP_NOT_FOUND);
            }

            $this->activateSubscription($userId, $entityManager);
        }

        return new JsonResponse(['status' => 'success']);
    }
    public function sendEmailConfirmation(string $address): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('***REMOVED***', 'Payment Confirmation MaakMed'))
            ->to($address)
            ->subject('Payment Confirmation MaakMed')
            ->htmlTemplate('subscription/confirmationPayment.html.twig');
        $this->mailer->send($email);
    }
    public function activateSubscription(int $patientId, EntityManagerInterface $entityManager): Response
    {
    error_log('In activateSubscription method for patient ID: ' . $patientId);
    $patient = $entityManager->getRepository(Patient::class)->find($patientId);
    if (!$patient) {
        throw $this->createNotFoundException('The patient does not exist');
    }
    $subscription = $entityManager->getRepository(Subscription::class)
        ->findOneBy([
            'patient' => $patient,
            'status' => ['pending', 'canceling']
        ]);

    if (!$subscription) {
                $this->addFlash('warning', 'No pending or canceling subscription found for this patient.');
                return $this->redirectToRoute('app_subscription_list');
    }
        $subscription->setStatus('active');
        $entityManager->persist($subscription);
        $entityManager->flush();
        return new Response('Subscription activated successfully');
    }
    #[Route('/cancel', name: 'app_subscription_Cancel',methods: ['POST'])]
    #[IsGranted("ROLE_PATIENT")]
    public function cancelSubscription(#[RequestParam] int $planId, EntityManagerInterface $entityManager): Response
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
            return $this->redirectToRoute('app_insurancePlan_list');
        }

        $subscription->setStatus('canceling');
        $entityManager->persist($subscription);
        $entityManager->flush();

        $this->addFlash('message', 'Subscription is being canceled.');
        $this->addFlash('status', 'success');

        return $this->redirectToRoute('app_insurancePlan_list');
    }

      #[Route('/success', name: 'app_subscription_success', methods: ['GET'])]
      #[IsGranted("ROLE_PATIENT")]
      public function paymentSuccess(): Response
        {
       return $this->render('subscription/success.html.twig');
        }
      #[Route('/failure', name: 'app_subscription_failure', methods: ['GET'])]
      #[IsGranted("ROLE_PATIENT")]
      public function paymentFailure(): Response
      {
          return $this->render('subscription/failure.html.twig');
      }
}
