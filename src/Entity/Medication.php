<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MedicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedicationRepository::class)]
#[ApiResource]
class Medication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $insured = null;

    #[ORM\OneToMany(mappedBy: 'medication', targetEntity: InventoryEntry::class, orphanRemoval: true)]
    private Collection $inventoryEntries;

    public function __construct()
    {
        $this->inventoryEntries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isInsured(): ?bool
    {
        return $this->insured;
    }

    public function setInsured(bool $insured): static
    {
        $this->insured = $insured;

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
            $inventoryEntry->setMedication($this);
        }

        return $this;
    }

    public function removeInventoryEntry(InventoryEntry $inventoryEntry): static
    {
        if ($this->inventoryEntries->removeElement($inventoryEntry)) {
            // set the owning side to null (unless already changed)
            if ($inventoryEntry->getMedication() === $this) {
                $inventoryEntry->setMedication(null);
            }
        }

        return $this;
    }
}
