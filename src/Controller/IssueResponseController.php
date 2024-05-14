<?php

namespace App\Controller;


use App\Entity\Admin;
use App\Entity\IssueResponse;
use App\Entity\Issue;
use App\Entity\User;
use App\Entity\Patient;


use App\Form\IssueResponseType;
use App\Form\IssueType;
use App\Form\IssueUpdateType;
use App\Repository\IssueRepository;
use App\Repository\IssueResponseRepository;
use App\Security\Voter\IssueResponseVoter;
use App\Security\Voter\IssueVoter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use DateTime;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;


#[Route('/issueResponse')]
class IssueResponseController extends AbstractController
{
    public function __construct(
        private readonly Breadcrumbs $breadcrumbs,
    ) {
    }

    #[Route('/', name: 'app_issue_response_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(IssueResponseRepository $responseRepository): Response
    {
        $this->breadcrumbs->addRouteItem('Responses', 'app_issue_response_index');

        // Fetch all responses along with their associated issues
        $responses = $responseRepository->findAllWithIssues();

        return $this->render('issueResponse/index.html.twig', [
            'responses' => $responses,
        ]);

    }

    #[Route('/new/{issueId}', name: 'app_add_issue_response')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager, $issueId): Response
    {
        $this->breadcrumbs->addRouteItem('Issues', 'app_issue_index');
        $this->breadcrumbs->addItem($issueId);
        $this->breadcrumbs->addRouteItem('Respond', 'app_add_issue_response', ['issueId' => $issueId]);

        // Fetch the issue by ID
        $issue = $entityManager->getRepository(Issue::class)->find($issueId);
        $user = $this->getUser();

        // Create a new response associated with the issue and set the respondent
        $response = new IssueResponse();
        $response->setIssue($issue);
        $response->setRespondent($user);

        // Set creation date to current date and time
        $response->setCreationDate(new \DateTimeImmutable());

        // Create the response form
        $form = $this->createForm(IssueResponseType::class, $response);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the response
            $entityManager->persist($response);
            $entityManager->flush();

            return $this->redirectToRoute('app_issue_response_index');
        }

        // Render the new response form
        return $this->render('issueResponse/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/response/{id}/edit', name: 'app_issue_response_edit', methods: ['GET', 'POST'])]
    #[IsGranted(IssueResponseVoter::EDIT, subject: 'Iresponse')]
    public function editResponse(Request $request, IssueResponse $Iresponse, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $this->breadcrumbs->addRouteItem('Responses', 'app_issue_response_index');
        $this->breadcrumbs->addItem($Iresponse->getId());
        $this->breadcrumbs->addRouteItem('Edit', 'app_issue_response_edit', ['id' => $Iresponse->getId()]);

        $form = $this->createForm(IssueResponseType::class, $Iresponse);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $logger->info('Form is submitted');

            if ($form->isValid()) {
                $logger->info('Form is valid');
                $entityManager->flush();
                return $this->redirectToRoute('app_issue_response_index');
            } else {
                $logger->info('Form is not valid');
            }
        }


        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );

        return $this->render('issueResponse/edit.html.twig', [
            'Iresponse' => $Iresponse,
            'form' => $form->createView(),
        ], $response);
    }



    #[Route('/response/{id}', name: 'app_issue_response_delete', methods: ['POST'])]
    #[IsGranted(IssueResponseVoter::EDIT, subject: 'Iresponse')]
    public function delete(Request $request, IssueResponse $Iresponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $Iresponse->getId(), $request->request->get('_token'))) {
            $entityManager->remove($Iresponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_issue_response_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/show-response/{id}', name: 'app_show_response', methods: ['GET'])]
    public function showResponse(IssueResponse $issueResponse): Response
    {
        $this->breadcrumbs->addRouteItem('Responses', 'app_issue_response_index');
        $this->breadcrumbs->addItem($issueResponse->getId());

        return $this->render('issueResponse/show_response.html.twig', [
            'response' => $issueResponse,
        ]);
    }



}



