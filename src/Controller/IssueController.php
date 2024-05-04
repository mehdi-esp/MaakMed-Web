<?php

namespace App\Controller;


use App\Entity\Admin;
use App\Entity\Issue;
use App\Entity\IssueResponse;
use App\Entity\Patient;

use App\Form\IssueType;
use App\Form\IssueUpdateType;
use App\Repository\IssueRepository;
use App\Security\Voter\IssueVoter;
use App\Service\IssueCategorizer;
use Doctrine\ORM\EntityManagerInterface;
use Jungi\FrameworkExtraBundle\Attribute\RequestParam;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Buchin\Badwords\Badwords;
use Symfony\Contracts\HttpClient\HttpClientInterface;


#[Route('/issue')]
class IssueController extends AbstractController
{

    private int $newIssueCount = 0;

    public function incrementNewIssueCount(): void
    {
        $this->newIssueCount++;
    }

    public function resetNewIssueCount(): void
    {
        $this->newIssueCount = 0;
    }

    public function isNew(Issue $issue): bool
    {
        $creationDate = $issue->getCreationDate();
        $oneDayAgo = (new \DateTime())->modify('-24 hours');

        return $creationDate > $oneDayAgo;
    }

    #[Route('/', name: 'app_issue_index', methods: ['GET'])]
    #[IsGranted(IssueVoter::LIST_ALL)]
    public function index(IssueRepository $issueRepository, AuthorizationCheckerInterface $authChecker): Response
    {
        $user = $this->getUser();

        // Check user role and set criteria accordingly
        if ($user instanceof Patient) {
            return $this->render('issue/index.html.twig');
        }

        // Fetch issues based on criteria
        $issues = $issueRepository->findAll();

        // Render the appropriate template
        return $this->render('issue/index_admin.html.twig', [
            'issues' => $issues,
        ]);
    }

    #[Route('/api/categorize', name: 'app_issue_categorize', methods: ['POST'])]
    public function categorize(
        #[RequestParam('content')] string $content,
        IssueCategorizer                  $issueCategorizer
    ): Response
    {
        $category = $issueCategorizer->categorize($content);
        return $this->json(['category' => $category]);
    }

    #[Route('/api/translate', name: 'app_issue_translate', methods: ['POST'])]
    public function translate(#[RequestParam] string $content, HttpClientInterface $httpClient): Response
    {
        $API_TOKEN = $_ENV['HF_API_TOKEN'] ?? null;

        if (!$API_TOKEN) {
            return new JsonResponse(['error' => 'API token not found'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = $httpClient->request(
            'POST',
            'https://api-inference.huggingface.co/models/Helsinki-NLP/opus-mt-en-fr',
            [
                'headers' => ['Authorization' => 'Bearer ' . $API_TOKEN],
                'json' => ['inputs' => $content]
            ]
        );

        $data = $response->toArray();
        $translation = $data[0]['translation_text'] ?? null;

        if (!$translation) {
            return new JsonResponse(['error' => 'Translation failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['translation' => $translation]);
    }


    #[Route('/new', name: 'app_addIssue')]
    #[IsGranted('ROLE_PATIENT')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $issue = new Issue();
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        $badWordDetected = false;

        if ($form->isSubmitted() && $form->isValid()) {
            // Check the content for bad words
            $content = $form->get('content')->getData();
            $isDirty = Badwords::isDirty($content);

            if ($isDirty) {
                // If bad words are found, set badWordDetected to true
                $badWordDetected = true;
            } else {

                // Set other issue properties
                $issue->setUser($this->getUser());
                $issue->setCreationDate(new \DateTimeImmutable());

                $entityManager->persist($issue);
                $entityManager->flush();

                $this->incrementNewIssueCount();

                return $this->redirectToRoute('app_issue_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );

        return $this->render('issue/add.html.twig', [
            'issue' => $issue,
            'form' => $form->createView(),
            'badWordDetected' => $badWordDetected
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


    public function getCategories(IssueRepository $issueRepository): Response
    {
        // Fetch the categories from your Issue repository
        $categories = $issueRepository->findAllCategories(); // Assuming you have a method like this in your repository

        // Return a JSON response with the categories
        return $this->json($categories);
    }

    #[Route('/latest', name: 'app_issue_latest', methods: ['GET'])]
    public function latest(IssueRepository $issueRepository): Response
    {
        // Fetch the latest issue
        $latestIssue = $issueRepository->findOneBy([], ['creationDate' => 'DESC']);

        // Return the issue as a JSON response
        return $this->json($latestIssue);
    }

    #[Route('/search', name: 'app_issue_search', methods: ['GET'])]
    public function search(IssueRepository $issueRepository, Request $request): Response
    {
        $category = $request->query->get('category');

        $issues = $issueRepository->findBy(array_filter(['category' => $category ?: null]));


        return $this->json($issues);
    }


}






