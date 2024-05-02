<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class IssueCategorizer
{
    private const BASE_URI = 'https://api-inference.huggingface.co/models';

    private const MODEL = 'facebook/bart-large-mnli';

    private string $token;

    private const LABELS = [
        "Pharmacy",
        "Medication",
        "Doctor",
        "Other"
    ];

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface $logger
    ) {
        $token = $_ENV['HF_API_TOKEN'] ?? null;
        if ($token === null) {
            throw new \Exception(
                "Hugging Face API token not found in environment variable HF_API_TOKEN, failed to initialize IssueCategorizer.",
            );
        }
        $this->token = $token;
    }

    public function categorize(string $content): string
    {
        $ranking = $this->retrieveSuggestion($content);

        $category = array_search(max($ranking), $ranking);

        return $category;
    }

    private function retrieveSuggestion(string $content): array
    {

        $json = [
            "inputs" => $content,
            "parameters" => [
                "candidate_labels" => self::LABELS,
            ]
        ];
        $response = $this->client->request("POST", self::BASE_URI . '/' . self::MODEL, [
            "json" => $json,
            "headers" => ["Authorization" => "Bearer {$this->token}"],
        ]);

        $result = $response->toArray();

        [
            "sequence" => $sequence,
            "labels" => $labels,
            "scores" => $scores,
        ] = $result;

        $transformed = array_combine($labels, $scores);

        return $transformed;
    }
}
