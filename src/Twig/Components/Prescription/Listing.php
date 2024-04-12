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
        /** @var Doctor|Patient|Admin $user */
        $user = $this->getUser();

        $qb = $this->prescriptionRepository->createQueryBuilder('p')
            ->innerJoin('p.visit', 'v');

        if ($user instanceof Doctor) {
            $qb->where('v.doctor = :user')
                ->setParameter('user', $user);
        } elseif ($user instanceof Patient) {
            $qb->where('v.patient = :user')
                ->setParameter('user', $user);
        }

        if (in_array($this->orderBy, ['creationDate', 'confirmed'])) {
            $qb->orderBy('p.' . $this->orderBy, $this->orderDir);
        }

        if ($this->confirmed !== null) {
            $qb->andWhere('p.confirmed = :confirmed')
                ->setParameter('confirmed', $this->confirmed);
        }

        return $qb->getQuery()->getResult();
    }

}