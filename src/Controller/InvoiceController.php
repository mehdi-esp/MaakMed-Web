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
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

#[Route('/invoice')]
class InvoiceController extends AbstractController
{
    public function __construct(
        private readonly Breadcrumbs $breadcrumbs,
    ) {
    }

    #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
    #[IsGranted(InvoiceVoter::LIST_ALL)]
    public function index(): Response
    {
        $this->breadcrumbs->addRouteItem("Invoices", "app_invoice_index");
        return $this->render('invoice/index.html.twig');
    }

    #[Route('/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PHARMACY')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $this->breadcrumbs->addRouteItem("Invoices", "app_invoice_index");
        $this->breadcrumbs->addRouteItem("New", "app_invoice_new");
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pharmacy = $this->getUser();
            $invoice->setPharmacy($pharmacy);
            $invoice->setCreationTime(new \DateTimeImmutable());

            $entityManager->persist($invoice);
            $entityManager->flush();

            $this->addFlash("success", "Successfully created invoice.");
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
        $this->breadcrumbs->addRouteItem("Invoices", "app_invoice_index");
        $this->breadcrumbs->addRouteItem($invoice->getId(), "app_invoice_show", ["id" => $invoice->getId()]);
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    #[IsGranted(InvoiceVoter::MANAGE, subject: 'invoice')]
    public function edit(
        Request                $request,
        Invoice                $invoice,
        EntityManagerInterface $entityManager
    ): Response {
        $this->breadcrumbs->addRouteItem("Invoices", "app_invoice_index");
        $this->breadcrumbs->addRouteItem($invoice->getId(), "app_invoice_show", ["id" => $invoice->getId()]);
        $this->breadcrumbs->addRouteItem("Edit", "app_invoice_edit", ["id" => $invoice->getId()]);

        if ($request->getMethod() === 'POST') {
            $payload = $request->request->all();
            $payload['invoice']['submitted'] = true;
            $request->request->replace($payload);
        }

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash("success", "Invoice changes saved.");
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
            $this->addFlash("info", "Invoice deleted.");
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}
