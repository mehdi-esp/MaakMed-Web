<?php

namespace App\Controller;
use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Repository\PrescriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\Voter\PrescriptionVoter;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Jungi\FrameworkExtraBundle\Attribute\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Prescription;
use App\Repository\VisitRepository;
use App\Form\PrescriptionType;




#[Route('/prescription')]
class PrescriptionController extends AbstractController
{
    #[Route('/', name: 'app_prescription_index', methods: ['GET'])]
    #[IsGranted(PrescriptionVoter::LIST_ALL)]
    public function index(
        PrescriptionRepository $prescriptionRepository,
        Request $request
    ): Response
    {
        /** @var Doctor|Admin|Patient $user */
        $user = $this->getUser();


        return $this->render('prescription/index.html.twig');
    }

    #[Route('/new', name: 'app_prescription_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_DOCTOR")]
    public function new(Request $request, EntityManagerInterface $em, VisitRepository $visitRepository): Response
    {
        $visitsWithoutPrescription = $visitRepository->createQueryBuilder('v')
            ->leftJoin('v.prescription', 'p')
            ->where('p.id IS NULL')
            ->getQuery()
            ->getResult();

        if (count($visitsWithoutPrescription) === 0) {
            $this->addFlash('warning', 'All visits have a prescription');
            return $this->redirectToRoute('app_prescription_index');
        }

        $prescription = new Prescription();
        $form = $this->createForm(PrescriptionType::class, $prescription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prescription->setCreationDate(new \DateTimeImmutable());
            $em->persist($prescription);
            $em->flush();

            return $this->redirectToRoute('app_prescription_index', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );

        return $this->render('prescription/new.html.twig', [
            'form' => $form->createView(),
            'prescription' => $prescription,
        ], $response);
    }

    #[Route('/{id}', name: 'app_prescription_show', methods: ['GET'])]
    #[IsGranted(PrescriptionVoter::VIEW, subject: 'prescription')]
    public function show(Prescription $prescription): Response
    {
        return $this->render('prescription/show.html.twig', [
            'prescription' => $prescription,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_prescription_edit', methods: ['GET', 'POST'])]
    #[IsGranted(PrescriptionVoter::MANAGE, subject: 'prescription')]
    public function edit(Request $request, Prescription $prescription, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() === 'POST') {
            $payload = $request->request->all();
            $payload['prescription']['submitted'] = true;
            $request->request->replace($payload);
        }

        $form = $this->createForm(PrescriptionType::class, $prescription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prescription_index', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );

        return $this->render('prescription/edit.html.twig', [
            'prescription' => $prescription,
            'form' => $form->createView(),
        ], $response);
    }
    #[Route('/{id}', name: 'app_prescription_delete', methods: ['POST'])]
    #[IsGranted(PrescriptionVoter::MANAGE, subject: 'prescription')]
    public function delete(Request $request, Prescription $prescription, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $prescription->getId(), $request->request->get('_token'))) {
            $entityManager->remove($prescription);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prescription_index', [], Response::HTTP_SEE_OTHER);
    }
}
