<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/news')]
class NewsController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/', name: 'app_news_index', methods: ['GET'])]
    #[IsGranted("ROLE_DOCTOR")]
    public function index(): Response
    {
        $response = $this->client->request('GET', 'https://newsapi.org/v2/top-headlines', [
            'query' => [
                'apiKey' => 'a5ffe43954f44e1588f6cfa23fed6a63',
                'category' => 'health',
                'language' => 'en',
            ],
        ]);

        $content = $response->toArray();

        return $this->render('news/index.html.twig', [
            'articles' => $content['articles'],
        ]);
    }
}