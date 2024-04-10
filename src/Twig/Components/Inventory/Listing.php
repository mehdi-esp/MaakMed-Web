<?php

namespace App\Twig\Components\Inventory;

use App\Entity\Pharmacy;
use App\Form\InventoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

/** @method Pharmacy getUser() */
#[AsLiveComponent]
class Listing extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {

    }

    #[LiveProp]
    public Pharmacy $initialFormData;

    #[LiveProp(writable: true)]
    public bool $editing = true;

    public function mount(): void
    {
        $this->initialFormData = $this->getUser();
    }

    #[LiveAction]
    public function save(): void
    {
        $this->formValues['submitted'] = true;
        $this->submitForm();

        if (!$this->form->isValid()) {
            return;
        }
        $this->editing = false;
        $this->entityManager->flush();
    }


    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(
            InventoryType::class,
            $this->initialFormData
        );
    }
}