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


#[Route('/medication')]
class MedicationController extends AbstractController
{
#[Route('/', name: 'app_medication_index', methods: ['GET'])]
    public function index(MedicationRepository $medicationRepository): Response
    {    /** @var Doctor|Admin|Pharmacy $user */
        $user = $this->getUser();
        $medications= $medicationRepository->findAll();
        return $this->render('medication/index.html.twig', [
            'medications' => $medications,
        ]);
    }

    #[Route('/new', name: 'app_medication_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
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
}
