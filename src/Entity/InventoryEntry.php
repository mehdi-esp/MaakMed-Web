<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\InventoryEntryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryEntryRepository::class)]
#[ApiResource]
class InventoryEntry
{
    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'inventoryEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pharmacy $pharmacy = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'inventoryEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medication $medication = null;

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function increment(int $amount = 1): static
    {
        $this->quantity ??= 0;
        $this->quantity += $amount;
        return $this;
    }

    public function decrement(int $amount = 1): static
    {
        $this->quantity ??= 0;
        $this->quantity -= $amount;
        return $this;
    }

    public function getPharmacy(): ?Pharmacy
    {
        return $this->pharmacy;
    }

    public function setPharmacy(?Pharmacy $pharmacy): static
    {
        $this->pharmacy = $pharmacy;

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
}
