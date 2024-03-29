<?php

namespace App\Twig\Components\Visit;

use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Visit;
use App\Entity\VisitCategory;
use App\Repository\UserRepository;
use App\Repository\VisitRepository;
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

    public function __construct(
        private readonly VisitRepository        $visitRepository,
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager,
        #[LiveProp(writable: true, url: true)]
        public ?Patient                         $patient = null,
        #[LiveProp(writable: true, url: true)]
        public ?Doctor                          $doctor = null,
        #[LiveProp(writable: true, url: true)]
        public ?VisitCategory                   $category = null,
        #[LiveProp(
            writable: true,
            hydrateWith: 'hydrateOrderDir',
            dehydrateWith: 'dehydrateOrderDir',
            url: true,
        )]
        public string                           $orderDir = "DESC",
        // #[LiveProp(writable: true, url: true)]
        public string                           $orderBy = "date"
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

    #[LiveAction]
    public function clearFilters(): void
    {
        $this->patient = null;
        $this->doctor = null;
        $this->category = null;
        $this->dispatchBrowserEvent('filters:clear');
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


        if ($this->doctor !== null && $this->getUser() instanceof Doctor) {
            $this->doctor = null;
        }

        if ($this->doctor?->getId() === null) {
            $this->doctor = null;
        }
    }

    public function getCategories(): array
    {
        return VisitCategory::cases();
    }

    /** @return Visit[] */
    public function getVisits(): array
    {
        $user = $this->getUser();

        $visibilityCriterion = match (true) {
            $user instanceof Doctor => ['doctor' => $user],
            $user instanceof Patient => ['patient' => $user],
            $user instanceof Admin => []
        };

        $filters = array_filter([
            'patient' => $this->patient,
            'doctor' => $this->doctor,
            'type' => $this->category
        ]);

        $criteria = array_merge($visibilityCriterion, $filters);

        $orderBy = [$this->orderBy => $this->orderDir];

        return $this->visitRepository->findBy($criteria, $orderBy);
    }
}
