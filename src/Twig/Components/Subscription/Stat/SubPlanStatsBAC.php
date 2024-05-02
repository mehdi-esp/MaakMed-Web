<?php

namespace App\Twig\Components\Subscription\Stat;

use App\Entity\subscription;
use App\Entity\InsurancePlan;
use App\Repository\SubscriptionRepository;
use App\Repository\InsurancePlanRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use DateTime;



#[AsLiveComponent]
class SubPlanStatsBAC
{
     use DefaultActionTrait;


    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly SubscriptionRepository  $SubscriptionRepository,
        private readonly InsurancePlanRepository  $InsurancePlanRepository,
    )
    {
    }
    public function getTotalRevenuePerPlan(): array
                         {
                             return $this->SubscriptionRepository->getTotalRevenuePerPlan();
                         }
     public function getTotalActiveSubscribers(): int
        {
            return $this->SubscriptionRepository->getTotalActiveSubscribers();
        }
     public function getActiveUsersPercentage(): float
         {
             return $this->SubscriptionRepository->getActiveUsersPercentage();
         }
     public function getCancelingUsersPercentage(): float
         {
             return $this->SubscriptionRepository->getCancelingUsersPercentage();
         }
      public function getTotalCancelingSubscribers(): int
         {
             return $this->SubscriptionRepository->getTotalCancelingSubscribers();
         }
         }

