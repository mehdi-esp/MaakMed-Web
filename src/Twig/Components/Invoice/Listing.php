<?php

namespace App\Twig\Components\Invoice;

use App\Entity\Admin;
use App\Entity\Invoice;
use App\Entity\Patient;
use App\Entity\Pharmacy;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class Listing extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
    )
    {
    }

    /** @return Invoice[] */
    public function getInvoices(): array
    {
        /** @var Patient|Pharmacy|Admin $user */
        $user = $this->getUser();

        $criteria = [];
        $visibilityCriterion = match (true) {
            $user instanceof Admin => [],
            $user instanceof Pharmacy => ['pharmacy' => $user],
            $user instanceof Patient => ['patient' => $user],
        };
        $criteria = array_merge($criteria, $visibilityCriterion);

        return $this->invoiceRepository->findBy($criteria);

    }

}