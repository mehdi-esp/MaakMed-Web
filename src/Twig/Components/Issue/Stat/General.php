<?php

namespace App\Twig\Components\Issue\Stat;

use App\Entity\IssueResponse;
use App\Repository\IssueRepository;
use App\Repository\IssueResponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class General extends AbstractController
{
    use DefaultActionTrait;

    public function __construct(
        private readonly IssueRepository         $issueRepository,
        private readonly IssueResponseRepository $issueResponseRepository
    )
    {

    }

    public function getAll(): int
    {
        return $this->issueRepository->count([]);
    }

    public function getRespondedTo(): int
    {
        // an issue can have multiple responses
        return $this->issueResponseRepository->createQueryBuilder('ir')
            ->select('count(DISTINCT ir.issue)')
            ->getQuery()
            ->getSingleScalarResult();

    }

    public function getUnrespondedTo(): int
    {
        return $this->issueRepository->createQueryBuilder('i')
            ->select('count(i)')
            ->leftJoin(IssueResponse::class, 'ir', 'WITH', 'ir.issue = i')
            ->where('ir.issue IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

}