<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Visit;
use App\Form\VisitType;
use App\Repository\UserRepository;
use App\Repository\VisitRepository;
use App\Security\Voter\VisitVoter;
use Doctrine\ORM\EntityManagerInterface;
use Jungi\FrameworkExtraBundle\Attribute\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/visit')]
class VisitController extends AbstractController
{
    #[Route('/', name: 'app_visit_index', methods: ['GET'])]
    #[IsGranted(VisitVoter::LIST_ALL)]
    public function index(
        VisitRepository                  $visitRepository,
        UserRepository                   $userRepository,
        #[QueryParam('doctor')] ?string  $doctorUsername = null,
        #[QueryParam('patient')] ?string $patientUsername = null,
    ): Response
    {
        /** @var Doctor|Admin|Patient $user */

        $user = $this->getUser();

        $criteria = [];

        $userCriteria = match (true) {
            $user instanceof Doctor => ['doctor' => $user],
            $user instanceof Patient => ['patient' => $user],
            $user instanceof Admin => []
        };

        /** @var Doctor|null $doctor */
        $doctor = null;

        if ($doctorUsername) {
            if ($user instanceof Doctor) {
                // create invalid request/argument exception
                throw new BadRequestHttpException('You cannot filter by doctor');
            }
            $doctor = $userRepository->findOneByUsername($doctorUsername);
            if (!$doctor instanceof Doctor) {
                throw new BadRequestHttpException('Doctor not found');
            }

            // valid
            $criteria['doctor'] = $doctor;
        }

        /** @var Patient|null $patient */
        $patient = null;

        if ($patientUsername) {
            if ($user instanceof Patient) {
                // create invalid request/argument exception
                throw new BadRequestHttpException('You cannot filter by patient');
            }
            $patient = $userRepository->findOneByUsername($patientUsername);
            if (!$patient instanceof Patient) {
                throw new BadRequestHttpException('Patient not found');
            }
            // valid
            $criteria['patient'] = $patient;
        }

        $criteria = [...$criteria, ...$userCriteria];

        $visits = $visitRepository->findBy($criteria);

        $filters = array_filter(['doctor' => $doctor, 'patient' => $patient]);
        return $this->render('visit/index.html.twig', [
            'visits' => $visits,
            'filters' => $filters
        ]);
    }

    #[Route('/new', name: 'app_visit_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_DOCTOR")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Doctor $doctor */
        $doctor = $this->getUser();

        $visit = new Visit();
        dump($visit);
        $form = $this->createForm(VisitType::class, $visit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($visit);
            $visit->setDate(new \DateTimeImmutable());
            $visit->setDoctor($doctor);
            $entityManager->persist($visit);
            $entityManager->flush();

            $this->addFlash('success', 'New visit created successfully!');

            return $this->redirectToRoute('app_visit_index', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK );

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

        dump($request->getPreferredFormat());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_visit_index', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK );

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
