<?php

namespace App\Twig\Components\User\Chart;

use App\Repository\UserRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class Stat
{
    use DefaultActionTrait;

    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly UserRepository       $userRepository,
    )
    {
    }


    public function getUsersWithEmail(): int
    {
        return $this->userRepository->getUsersWithEmail();
    }

    public function getUnverifiedUsers(): int
    {
        return $this->userRepository->getUnverifiedUsers();
    }
    public function getUsersWithNumber(): int
    {
        return $this->userRepository->getUsersWithNumber();
    }
}