<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\InvoiceEntryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceEntryRepository::class)]
#[ApiResource]
class InvoiceEntry
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'invoiceEntries')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Invoice $invoice = null;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Medication $medication = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $cost = null;

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): static
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getMedication(): ?Medication
    {
        return $this->medication;
    }

    public function setMedication(?Medication $medication): static
    {
        $this->medication = $medication;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(?float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }


    /** @param $entries Collection<int, InvoiceEntry> */
    public static function computeReimbursementRate(Collection $entries, ?InsurancePlan $plan): float
    {
        if ($plan === null) {
            return 0;
        }

        $patientPlanReimbursementRate = $plan->getReimbursementRate();
        $ceiling = $plan->getCeiling();

        $totalCost = 0;
        $totalReimbursement = 0;

        foreach ($entries as $entry) {
            $cost = $entry->getCost() * $entry->getQuantity();
            $totalCost += $cost;
            $medication = $entry->getMedication();
            if ($medication->isInsured()) {
                $totalReimbursement += $cost * $patientPlanReimbursementRate;
            }
        }

        $actualReimbursement = min($totalReimbursement, $ceiling);

        return $actualReimbursement / $totalCost;
    }

}
