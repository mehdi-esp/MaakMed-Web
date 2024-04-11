<?php

namespace App\Twig\Components\Medication;

use App\Entity\Medication;
use App\Repository\MedicationRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[LiveProp(writable: true, url: true)]
    public ?string $orderBy = 'name';

    #[LiveProp(writable: true, url: true)]
    public ?string $orderDir = 'ASC';

    #[LiveProp(writable: true, url: true)]
    public ?bool $insured = null;

    #[LiveProp(writable: true, url: true)]
    public ?string $searchTerm = null;

    public function __construct(
        private readonly MedicationRepository  $medicationRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[LiveAction]
    public function clearFilters(): void
    {
        $this->orderBy = 'name';
        $this->orderDir = 'ASC';
        $this->insured = null;
        $this->searchTerm = null;
        $this->dispatchBrowserEvent('filters:clear');
    }

    #[PostMount]
    public function postMount(): void
    {
        // TODO: Maybe inform the user of improper query param usage or throw exceptions?
    }

    /** @return Medication[] */
    public function getMedications(): array
    {
        $criteria = [];
        if ($this->insured !== null) {
            $criteria['insured'] = $this->insured;
        }

        $orderBy = [$this->orderBy => $this->orderDir];

        $medications = $this->medicationRepository->findBy($criteria, $orderBy);

        // If searchTerm is set, filter the medications based on the searchTerm
        if ($this->searchTerm !== null) {
            $medications = array_filter($medications, function($medication) {
                return strpos(strtolower($medication->getName()), strtolower($this->searchTerm)) === 0;
            });
        }

        return $medications;
    }
}