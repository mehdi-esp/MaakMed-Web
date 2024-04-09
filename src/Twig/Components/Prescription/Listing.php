<?php

namespace App\Twig\Components\Prescription;

use App\Entity\Prescription;
use App\Repository\PrescriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;
use App\Entity\{Admin, Doctor, Patient};
#[AsLiveComponent()]
final class Listing extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp(writable: true, url: true)]
    public ?string $orderBy = 'creationDate';

    #[LiveProp(writable: true, url: true)]
    public ?string $orderDir = 'ASC';

    #[LiveProp(writable: true, url: true)]
    public ?bool $confirmed = null;

    public function __construct(
        private readonly PrescriptionRepository  $prescriptionRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[LiveAction]
    public function clearFilters(): void
    {
        $this->orderBy = 'creationDate';
        $this->orderDir = 'ASC';
        $this->confirmed = null;
        $this->dispatchBrowserEvent('filters:clear');
    }

    #[PostMount]
    public function postMount(): void
    {
        // TODO: Maybe inform the user of improper query param usage or throw exceptions?
    }

    /** @return Prescription[] */
    public function getPrescriptions(): array
    {
        $user = $this->getUser();

        $prescriptions = $this->prescriptionRepository->findByUserAndFilters($user, $this->confirmed, $this->orderBy);
        $filteredPrescriptions = [];
        foreach ($prescriptions as $prescription) {
            if ($user instanceof Doctor && $prescription->getVisit()->getDoctor() === $user) {
                $filteredPrescriptions[] = $prescription;
            } elseif ($user instanceof Patient) {
                $patient = $this->getPatientFromPrescription($prescription);
                if ($patient === $user) {
                    $filteredPrescriptions[] = $prescription;
                }
            }
        }

        // If confirmed is set, filter the prescriptions based on the confirmed status
        if ($this->confirmed !== null) {
            $filteredPrescriptions = array_filter($filteredPrescriptions, function($prescription) {
                return $prescription->isConfirmed() === $this->confirmed;
            });
        }

        // If orderBy is set, sort the prescriptions based on the orderBy property
        if ($this->orderBy !== null) {
            usort($filteredPrescriptions, function($a, $b) {
                if ($this->orderBy === 'creationDate') {
                    return $this->orderDir === 'ASC' ? $a->getCreationDate() <=> $b->getCreationDate() : $b->getCreationDate() <=> $a->getCreationDate();
                } elseif ($this->orderBy === 'confirmed') {
                    return $this->orderDir === 'ASC' ? $a->isConfirmed() <=> $b->isConfirmed() : $b->isConfirmed() <=> $a->isConfirmed();
                }
            });
        }

        return $filteredPrescriptions;
    }
    public function getPatientFromPrescription(Prescription $prescription): ?Patient
    {
        $visit = $prescription->getVisit();

        if ($visit !== null) {
            return $visit->getPatient();
        }

        return null;
    }
}