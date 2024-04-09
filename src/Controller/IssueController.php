<?php

namespace App\Controller;


use App\Entity\Admin;
use App\Entity\Issue;
use App\Entity\Patient;

use App\Form\IssueType;
use App\Form\IssueUpdateType;
use App\Repository\IssueRepository;
use App\Security\Voter\IssueVoter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


#[Route('/issue')]
class IssueController extends AbstractController
{
    #[Route('/', name: 'app_issue_index', methods: ['GET'])]
    #[IsGranted(IssueVoter::LIST_ALL)]
    public function index(IssueRepository $issueRepository, AuthorizationCheckerInterface $authChecker): Response
    {
        $user = $this->getUser();

        $criteria = [];
        // Check user role and set criteria accordingly
        if ($authChecker->isGranted('ROLE_ADMIN')) {
            // For admins, no additional criteria needed, fetch all issues
        } else {
            // For patients, fetch issues associated with the current patient
            $criteria['user'] = $user;
        }

        // Fetch issues based on criteria
        $issues = $issueRepository->findBy($criteria);

        // Render the appropriate template
        $template = $authChecker->isGranted('ROLE_ADMIN') ? 'issue/index_admin.html.twig' : 'issue/index.html.twig';

        return $this->render($template, [
            'issues' => $issues,
        ]);
    }


    #[Route('/new', name: 'app_addIssue')]
    #[IsGranted('ROLE_PATIENT')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $issue = new Issue();
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUser()->addIssue($issue);

            // Set creation date to current date and time
            $issue->setCreationDate(new \DateTimeImmutable());

            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->redirectToRoute('app_issue_index', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );

        return $this->render('issue/add.html.twig', [
            'issue' => $issue,
            'form' => $form->createView()
        ], $response);
    }


    #[Route('/{id}/edit', name: 'app_issue_edit', methods: ['GET', 'POST'])]
    #[IsGranted(IssueVoter::EDIT, subject: 'issue')]
    public function edit(Request $request, Issue $issue, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $form = $this->createForm(IssueUpdateType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $logger->info('Form is submitted');

            if ($form->isValid()) {
                $logger->info('Form is valid');
                $entityManager->flush();
                return $this->redirectToRoute('app_issue_index');
            } else {
                $logger->info('Form is not valid');
            }
        }


        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );

        return $this->render('issue/edit.html.twig', [
            'issue' => $issue,
            'form' => $form->createView(),
        ], $response);
    }


    #[Route('/{id}', name: 'app_issue_delete', methods: ['POST'])]
    #[IsGranted(IssueVoter::EDIT, subject: 'issue')]
    public function delete(Request $request, Issue $issue, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $issue->getId(), $request->request->get('_token'))) {
            $entityManager->remove($issue);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_issue_index', [], Response::HTTP_SEE_OTHER);
    }
}






