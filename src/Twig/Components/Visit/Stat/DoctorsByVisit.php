<?php

namespace App\Twig\Components\Visit\Stat;

use App\Repository\VisitRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
class DoctorsByVisit
{
    use DefaultActionTrait;

    public function __construct(
        private readonly VisitRepository $visitRepository,
        #[LiveProp(writable: true)]
        public int $max = 3
    ) {
    }

    public function getDoctorsSortedByVisitCount(): array
    {
        $qb = $this->visitRepository->createQueryBuilder('v')
            ->addSelect('doctor, COUNT(v) visitCount')
            ->leftJoin('v.doctor', 'doctor')
            ->groupBy('doctor')
            ->orderBy('visitCount', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
