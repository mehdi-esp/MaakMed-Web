<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Invoice;
use App\Entity\Patient;
use App\Entity\Pharmacy;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use App\Security\Voter\InvoiceVoter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invoice')]
class InvoiceController extends AbstractController
{
    #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
    #[IsGranted(InvoiceVoter::LIST_ALL)]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        /** @var Pharmacy|Patient|Admin $user */
        $user = $this->getUser();

        $criteria = [];
        $visibilityCriterion = match (true) {
            $user instanceof Admin => [],
            $user instanceof Pharmacy => ['pharmacy' => $user],
            $user instanceof Patient => ['patient' => $user],
        };
        $criteria = array_merge($criteria, $visibilityCriterion);

        $invoices = $invoiceRepository->findBy($criteria);
        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PHARMACY')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pharmacy = $this->getUser();
            $invoice->setPharmacy($pharmacy);
            $invoice->setCreationTime(new \DateTimeImmutable());

            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );

        return $this->render('invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form->createView()
        ], $response);
    }

    #[Route('/{id}', name: 'app_invoice_show', methods: ['GET'])]
    #[IsGranted(InvoiceVoter::VIEW, subject: 'invoice')]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    #[IsGranted(InvoiceVoter::MANAGE, subject: 'invoice')]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() === 'POST') {
            $payload = $request->request->all();
            $payload['invoice']['submitted'] = true;
            $request->request->replace($payload);
        }

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form->createView(),
        ], $response);
    }

    #[Route('/{id}', name: 'app_invoice_delete', methods: ['POST'])]
    #[IsGranted(InvoiceVoter::MANAGE, subject: 'invoice')]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}
