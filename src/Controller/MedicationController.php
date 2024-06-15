<?php

namespace App\Controller;

use App\Entity\Medication;
use App\Form\MedicationType;
use App\Repository\MedicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\Voter\MedicationVoter;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;


#[Route('/medication')]
class MedicationController extends AbstractController
{
    public function __construct(
        private readonly Breadcrumbs $breadcrumbs,
    ) {

    }
#[Route('/', name: 'app_medication_index', methods: ['GET'])]
    public function index(MedicationRepository $medicationRepository): Response
    {
        $this->breadcrumbs->addRouteItem("Medications", "app_medication_index");

        return $this->render('medication/index.html.twig' );
    }

    #[Route('/new', name: 'app_medication_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->breadcrumbs->addRouteItem("Medications", "app_medication_index");
        $this->breadcrumbs->addRouteItem("New", "app_medication_new");
        $medication = new Medication();
        $form = $this->createForm(MedicationType::class, $medication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($medication);
            $entityManager->flush();

            $this->addFlash('success', 'New medication created successfully!');

            return $this->redirectToRoute('app_medication_index', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );

        return $this->render('medication/new.html.twig', [
            'medication' => $medication,
            'form' => $form->createView(),
        ], $response);
    }
    #[Route('/{id}/edit', name: 'app_medication_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, Medication $medication, EntityManagerInterface $entityManager): Response
    {
        $this->breadcrumbs->addRouteItem("Medications", "app_medication_index");
        $this->breadcrumbs->addItem($medication->getId());
        $this->breadcrumbs->addRouteItem("Edit", "app_medication_edit", ["id" => $medication->getId()]);

        $form = $this->createForm(MedicationType::class, $medication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_medication_index', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK
        );

        return $this->render('medication/edit.html.twig', [
            'medication' => $medication,
            'form' => $form->createView(),
        ], $response);
    }
    #[Route('/{id}', name: 'app_medication_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(Request $request, Medication $medication, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$medication->getId(), $request->request->get('_token'))) {
            $entityManager->remove($medication);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_medication_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/summarize', name: 'app_medication_summarize', methods: ['POST', 'GET'])]
    public function summarize(Request $request, Medication $medication, HttpClientInterface $client): Response
    {

        $this->breadcrumbs->addRouteItem("Medications", "app_medication_index");
        $this->breadcrumbs->addItem($medication->getId());
        $this->breadcrumbs->addRouteItem("Summarization  " , "app_medication_summarize", ["id" => $medication->getId()]);
        $response = $client->request('POST', 'https://api.deepgram.com/v1/read?summarize=true&language=en', [
            'headers' => [
                'Authorization' => "Token {$_ENV['DEEPGRAM_TOKEN']}",
                'Content-Type' => 'application/json',
            ],
            'json' => ['text' => $medication->getDescription()],
        ]);

        $content = $response->getContent();
        $contentArray = json_decode($content, true);
        $summaryText = $contentArray['results']['summary']['text'];

        return $this->render('medication/summary.html.twig', [
            'summaryText' => $summaryText,
        ]);
    }
}
