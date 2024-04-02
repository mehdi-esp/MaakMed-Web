<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ApiResource]
#[ORM\HasLifecycleCallbacks]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $discountRate = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pharmacy $pharmacy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationTime = null;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceEntry::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $invoiceEntries;

    public function __construct()
    {
        $this->invoiceEntries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscountRate(): ?float
    {
        return $this->discountRate;
    }

    public function setDiscountRate(float $discountRate): static
    {
        $this->discountRate = $discountRate;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;

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

    public function getCreationTime(): ?\DateTimeInterface
    {
        return $this->creationTime;
    }

    public function setCreationTime(\DateTimeInterface $creationTime): static
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    /**
     * @return Collection<int, InvoiceEntry>
     */
    public function getInvoiceEntries(): Collection
    {
        return $this->invoiceEntries;
    }

    public function addInvoiceEntry(InvoiceEntry $invoiceEntry): static
    {
        if (!$this->invoiceEntries->contains($invoiceEntry)) {
            $this->invoiceEntries->add($invoiceEntry);
            $invoiceEntry->setInvoice($this);
        }

        return $this;
    }

    public function getTotal(): float
    {
        return $this->invoiceEntries->reduce(
            fn(float $sum, InvoiceEntry $entry) => $entry->getTotalCost() + $sum,
            0
        );
    }

    public function getReimbursed(): float
    {
        return $this->getTotal() * ($this->getDiscountRate());
    }

    public function getTotalPaid(): float
    {
        return $this->getTotal() * (1 - $this->getDiscountRate());

    }

    public function removeInvoiceEntry(InvoiceEntry $invoiceEntry): static
    {
        if ($this->invoiceEntries->removeElement($invoiceEntry)) {
            // set the owning side to null (unless already changed)
            if ($invoiceEntry->getInvoice() === $this) {
                $invoiceEntry->setInvoice(null);
            }
        }

        return $this;
    }
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function computeAndSetReimbursementRate(): void
    {
        $reimbursementRate = InvoiceEntry::computeReimbursementRate($this->getInvoiceEntries(), $this->getPatient()?->getActivePlan());
        $this->setDiscountRate($reimbursementRate);
    }
}
