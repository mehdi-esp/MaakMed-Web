<?php

namespace App\Twig\Components;

use App\Entity\Invoice;
use App\Form\InvoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent()]
final class InvoiceForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp]
    public ?Invoice $initialFormData;

    public ?string $buttonLabel = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(InvoiceType::class, $this->initialFormData);
    }
}
