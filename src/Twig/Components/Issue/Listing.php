<?php

namespace App\Twig\Components\Issue;

use App\Entity\Patient;
use App\Repository\IssueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
class Listing extends AbstractController
{
    use DefaultActionTrait;

    public function __construct(
        private readonly IssueRepository $issueRepository,
        #[LiveProp(writable: true, url: true)]
        public ?string                   $query = null,
        #[LiveProp(
            writable: true,
            hydrateWith: 'hydrateOrderDir',
            dehydrateWith: 'dehydrateOrderDir',
            url: true,
        )]
        public string                    $orderDir = 'ASC',
        #[LiveProp(writable: true, url: true)]
        public ?string                   $category = null,
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

    public function getIssues(): array
    {
        $user = $this->getUser();
        $queryBuilder = $this->issueRepository->createQueryBuilder('i');

        if ($user instanceof Patient) {
            $queryBuilder->andWhere('i.user = :patient')
                ->setParameter('patient', $user);
        }

        if ($this->query) {
            $queryBuilder->andWhere('i.title LIKE :query')
                ->setParameter('query', '%' . $this->query . '%');
        }

        if ($this->category) {
            $queryBuilder->andWhere('i.category = :category')
                ->setParameter('category', $this->category);
        }

        $queryBuilder->orderBy('i.creationDate', $this->orderDir);

        return $queryBuilder->getQuery()->getResult();


    }

}