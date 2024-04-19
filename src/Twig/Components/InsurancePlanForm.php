<?php

namespace App\Twig\Components;

use App\Entity\InsurancePlan;
use App\Form\InsurancePlanType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent()]
final class InsurancePlanForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp]
    public ?InsurancePlan $initialFormData;

    public ?string $buttonLabel = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(InsurancePlanType::class, $this->initialFormData);
    }
}