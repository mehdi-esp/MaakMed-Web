<?php

namespace App\Twig\Components\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class Listing
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, url: true)]
    public ?string $query = null;

    #[LiveProp(writable: true, url: true)]
    public string $sort = 'asc';
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /** @return User[] */
    public function getUsers(): array
    {
        $queryBuilder = $this->userRepository->createQueryBuilder('u');

        if ($this->query) {
            $queryBuilder->where('u.username LIKE :query')
                ->setParameter('query', '%' . $this->query . '%');
        }

        $queryBuilder->orderBy('u.username', $this->sort);

        return $queryBuilder->getQuery()->getResult();
    }
}

