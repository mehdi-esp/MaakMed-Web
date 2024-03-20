<?php

namespace App\Twig\Components\Visit;

use App\Entity\Visit;
use App\Form\VisitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\Form\FormInterface;

#[AsLiveComponent()]
final class Form extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Visit $initialFormData = null;

    public ?string $buttonLabel = null;

    protected function instantiateForm(): FormInterface
    {
        // we can extend AbstractController to get the normal shortcuts
        return $this->createForm(VisitType::class, $this->initialFormData);
    }
}
