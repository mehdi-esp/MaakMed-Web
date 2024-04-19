<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PrescriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrescriptionRepository::class)]
#[ApiResource]
class Prescription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column]
    private ?bool $confirmed = false;

    #[ORM\OneToMany(mappedBy: 'prescription', targetEntity: PrescriptionEntry::class, orphanRemoval: true,cascade: ['persist'])]
    private Collection $medications;

    #[ORM\OneToOne(mappedBy: 'prescription', cascade: ['persist', 'remove'])]
    private ?Visit $visit = null;

    public function __construct()
    {
        $this->medications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setconfirmed(bool $status): static
    {
        $this->confirmed = $status;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return Collection<int, PrescriptionEntry>
     */
    public function getMedications(): Collection
    {
        return $this->medications;
    }

    public function addMedication(PrescriptionEntry $medication): static
    {
        if (!$this->medications->contains($medication)) {
            $this->medications->add($medication);
            $medication->setPrescription($this);
        }

        return $this;
    }

    public function removeMedication(PrescriptionEntry $medication): static
    {
        if ($this->medications->removeElement($medication)) {
            // set the owning side to null (unless already changed)
            if ($medication->getPrescription() === $this) {
                $medication->setPrescription(null);
            }
        }

        return $this;
    }


    public function getVisit(): ?Visit
    {
        return $this->visit;
    }

    public function setVisit(Visit $visit): static
    {
        // set the owning side of the relation if necessary
        if ($visit->getPrescription() !== $this) {
            $visit->setPrescription($this);
        }

        $this->visit = $visit;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrescriptionSpeech(): string
    {
        $message = "Follow these instructions carefully: ";

        foreach ( $this->getMedications() as $medication) {
            $message .= $medication->getMedication()->getName() ." Please". $medication->getInstructions();
        }

        return $message;
    }
}
