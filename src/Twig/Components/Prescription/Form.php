<?php

namespace App\Twig\Components\Prescription;

use App\Entity\Prescription;
use App\Form\PrescriptionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\Component\Form\FormInterface;

#[AsLiveComponent()]
final class Form extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp]
    public ?Prescription $initialFormData;

    public ?string $buttonLabel = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PrescriptionType::class, $this->initialFormData);
    }
}