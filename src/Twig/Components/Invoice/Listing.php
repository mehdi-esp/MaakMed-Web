<?php

namespace App\Twig\Components\Invoice;

use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Invoice;
use App\Entity\Patient;
use App\Entity\Pharmacy;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsLiveComponent()]
final class Listing extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        public string                      $orderBy = "creationTime",
        #[LiveProp(
            writable: true,
            hydrateWith: 'hydrateOrderDir',
            dehydrateWith: 'dehydrateOrderDir',
            url: true,
        )]
                                           /** @var 'DESC'|'ASC' $orderDir */
        public string $orderDir = "DESC",

        #[LiveProp(writable: true, url: true)]
        public ?Patient                    $patient = null,
        #[LiveProp(writable: true, url: true)]
        public ?Pharmacy                   $pharmacy = null,
    )
    {
    }

    public function dehydrateOrderDir(string $orderDir): bool
    {
        return match ($orderDir) {
            'DESC' => true,
            'ASC' => false,
        };
    }

    public function hydrateOrderDir(bool $data): string
    {
        return match ($data) {
            false => 'ASC',
            default => 'DESC'
        };
    }


    #[PostMount]
    public function postMount(): void
    {
        // TODO: Maybe inform the user of improper query param usage or throw exceptions?

        if ($this->patient !== null && $this->getUser() instanceof Patient) {
            $this->patient = null;
        }

        if ($this->patient?->getId() === null) {
            $this->patient = null;
        }


        if ($this->pharmacy !== null && $this->getUser() instanceof Pharmacy) {
            $this->pharmacy = null;
        }

        if ($this->pharmacy?->getId() === null) {
            $this->pharmacy = null;
        }
    }


    #[LiveAction]
    public function clearFilters(): void
    {
        $this->patient = null;
        $this->pharmacy = null;
        $this->dispatchBrowserEvent('filters:clear');
    }

    /** @return Invoice[] */
    public function getInvoices(): array
    {
        /** @var Patient|Pharmacy|Admin $user */
        $user = $this->getUser();

        $visibilityCriterion = match (true) {
            $user instanceof Admin => [],
            $user instanceof Pharmacy => ['pharmacy' => $user],
            $user instanceof Patient => ['patient' => $user],
        };
        $filters = array_filter([
            'patient' => $this->patient,
            'pharmacy' => $this->pharmacy,
        ]);

        $criteria = array_merge($visibilityCriterion, $filters);

        $ordering = [$this->orderBy => $this->orderDir];

        return $this->invoiceRepository->findBy($criteria, $ordering);

    }

}