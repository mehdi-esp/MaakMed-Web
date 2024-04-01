<?php

namespace App\Twig\Components\Visit;

use App\Entity\Visit;
use App\Form\VisitType;
use Kambo\Huggingface\Client;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\Form\FormInterface;
use Kambo\Huggingface\Huggingface;
use Kambo\Huggingface\Enums\Type;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

#[AsLiveComponent()]
final class Form extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    // XXX: Most fitting model is yet to be determined
    private const MODEL = 'bigscience/bloom';

    public ?Client $client = null;

    public function __construct(private readonly LoggerInterface $logger)
    {
        $token = $_ENV['HF_API_TOKEN'] ?? null;
        if ($token === null) {
            $logger->error(
                "Hugging Face API token not found in environment variable HF_API_TOKEN. Predictions will be disabled."
            );
            return;
        }
        $this->client = Huggingface::client($token);
    }

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Visit $initialFormData = null;

    #[LiveProp(writable: true)]
    public ?string $suggestion = null;

    public ?string $buttonLabel = null;

    protected function instantiateForm(): FormInterface
    {
        // we can extend AbstractController to get the normal shortcuts
        return $this->createForm(VisitType::class, $this->initialFormData);
    }

    #[LiveAction]
    public function genCompletion(#[LiveArg] string $diagnosis): void
    {
        $suggestion =  $this->completeDiagnosis($diagnosis);
        $this->suggestion = $suggestion;
    }

    private function completeDiagnosis(string $diagnosis, int $maxTokens = 5): string
    {
        $result = $this->client
            ->inference()
            ->create(
                [
                    'model' => self::MODEL,
                    'inputs' => $diagnosis,
                    'parameters' => [
                        "return_full_text" => false,
                        "max_new_tokens" => $maxTokens
                    ],
                    'type' => Type::TEXT_GENERATION,
                ]
            )->toArray();

        return $result['generated_text'];
    }
}
