<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\User;
use App\Entity\Visit;
use App\Form\VisitType;
use App\Security\Voter\VisitVoter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/visit')]
class VisitController extends AbstractController
{
    #[Route('/', name: 'app_visit_index', methods: ['GET'])]
    #[IsGranted(VisitVoter::LIST_ALL)]
    public function index(): Response
    {
        return $this->render('visit/index.html.twig');
    }

    #[Route('/_userinfo/{username}', name: '_app_visit_userinfo', methods: ['GET'])]
    #[IsGranted(VisitVoter::CONSULT_USER_INFO, subject: 'subject')]
    public function userInfoPopover(User $subject): Response
    {
        if (!$subject instanceof Patient && !$subject instanceof Doctor) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }
        /** @var Doctor|Patient|Admin $user */
        $user = $this->getUser();

        // XXX: Maybe use query builder for better performance?

        $count = match (true) {
            $user instanceof Admin => $subject->getVisits()->count(),
            $user instanceof Doctor => $subject->getVisits()
                ->filter(fn (Visit $visit) => $visit->getDoctor() === $user)
                ->count(),
            $user instanceof Patient => $subject->getVisits()
                ->filter(fn (Visit $visit) => $visit->getPatient() === $user)
                ->count(),
        };

        return $this->render('visit/popover/_userinfo.html.twig', [
            'count' => $count,
        ]);
    }

    #[Route('/new', name: 'app_visit_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_DOCTOR")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Doctor $doctor */
        $doctor = $this->getUser();

        $visit = new Visit();
        $form = $this->createForm(VisitType::class, $visit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $visit->setDate(new \DateTimeImmutable());
            $visit->setDoctor($doctor);
            $entityManager->persist($visit);
            $entityManager->flush();

            $this->addFlash('success', 'New visit created successfully!');

            return $this->redirectToRoute('app_visit_index', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );

        return $this->render('visit/new.html.twig', [
            'visit' => $visit,
            'form' => $form->createView(),
        ], $response);
    }

    #[Route('/{id}', name: 'app_visit_show', methods: ['GET'])]
    #[IsGranted(VisitVoter::VIEW, subject: 'visit')]
    public function show(Visit $visit): Response
    {
        return $this->render('visit/show.html.twig', [
            'visit' => $visit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_visit_edit', methods: ['GET', 'POST'])]
    #[IsGranted(VisitVoter::MANAGE, subject: 'visit')]
    public function edit(Request $request, Visit $visit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VisitType::class, $visit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_visit_index', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK
        );

        return $this->render('visit/edit.html.twig', [
            'visit' => $visit,
            'form' => $form->createView(),
        ], $response);
    }

    #[Route('/{id}', name: 'app_visit_delete', methods: ['POST'])]
    #[IsGranted(VisitVoter::MANAGE, subject: 'visit')]
    public function delete(Request $request, Visit $visit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $visit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($visit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_visit_index', [], Response::HTTP_SEE_OTHER);
    }
}
