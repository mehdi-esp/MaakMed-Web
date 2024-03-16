<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Pharmacy extends User
{
    use RegularUser;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $address = null;

    #[ORM\OneToMany(mappedBy: 'pharmacy', targetEntity: InventoryEntry::class, orphanRemoval: true)]
    private Collection $inventoryEntries;

    #[ORM\OneToMany(mappedBy: 'pharmacy', targetEntity: Invoice::class, orphanRemoval: true)]
    private Collection $invoices;

    public function __construct()
    {
        parent::__construct();
        $this->inventoryEntries = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, InventoryEntry>
     */
    public function getInventoryEntries(): Collection
    {
        return $this->inventoryEntries;
    }

    public function addInventoryEntry(InventoryEntry $inventoryEntry): static
    {
        if (!$this->inventoryEntries->contains($inventoryEntry)) {
            $this->inventoryEntries->add($inventoryEntry);
            $inventoryEntry->setPharmacy($this);
        }

        return $this;
    }

    public function removeInventoryEntry(InventoryEntry $inventoryEntry): static
    {
        if ($this->inventoryEntries->removeElement($inventoryEntry)) {
            // set the owning side to null (unless already changed)
            if ($inventoryEntry->getPharmacy() === $this) {
                $inventoryEntry->setPharmacy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setPharmacy($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getPharmacy() === $this) {
                $invoice->setPharmacy(null);
            }
        }

        return $this;
    }
}
