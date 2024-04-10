<?php

namespace App\Twig\Components\Medication;

use App\Entity\Medication;
use App\Form\MedicationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent()]
final class Form extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp]
    public ?Medication $initialFormData;

    public ?string $buttonLabel = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MedicationType::class, $this->initialFormData);
    }
}
