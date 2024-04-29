<?php

namespace App\Twig\Components\Subscription;

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

#[AsLiveComponent]
class SubPlanChart
{
    use DefaultActionTrait;

     #[ExposeInTemplate]
        private array $planWithMostSubscribers;

        #[ExposeInTemplate]
        private array $planWithMostCanceledStatus;

         #[ExposeInTemplate]
         private array $TotalRevenuePerPlan;

        #[ExposeInTemplate]
        private array $planWithMostCancelingStatus;
        #[LiveProp(writable: true)]
        public array $distinctStatuses = ['active','pending','canceling','canceled'];



    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly SubscriptionRepository  $SubscriptionRepository,
        private readonly InsurancePlanRepository  $InsurancePlanRepository,
    )
    {
            $this->planWithMostSubscribers = $this->SubscriptionRepository->getPlanWithMostSubscribers();
            $this->planWithMostCanceledStatus = $this->SubscriptionRepository->getPlanWithMostCanceledStatus();
            $this->planWithMostCancelingStatus = $this->SubscriptionRepository->getPlanWithMostCancelingStatus();
            $this->TotalRevenuePerPlan = $this->SubscriptionRepository->getTotalRevenuePerPlan();
    }

       public function getPlanWithMostSubscribers(): array
         {
             return $this->planWithMostSubscribers;
         }
         public function getPlanWithMostCanceledStatus(): array
         {
             return $this->planWithMostCanceledStatus;
         }

         public function getPlanWithMostCancelingStatus(): array
         {
             return $this->planWithMostCancelingStatus;
         }
         public function getTotalRevenuePerPlan(): array
         {
             return $this->TotalRevenuePerPlan;
         }


    public function getChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

       $data = $this->getChartData();

              $chart->setData($data);

              $chart->setOptions([
                  'plugins' => [
                      'title' => [
                          'display' => true,
                          'text' => 'Subscribers X Plans  distribution',
                      ],
                  ],
              ]);

        return $chart;
    }

    private function getChartData(): array
    {

          $queryBuilder = $this->SubscriptionRepository->createQueryBuilder('s')
            ->join('s.plan', 'p')
            ->select('p.name as plan, SUBSTRING(s.startDate, 6, 2) as month, COUNT(s.id) as occurrences')
            ->groupBy('p.name', 'month')
            ->orderBy('month', 'ASC');
        if (!empty($this->distinctStatuses)) {
            $queryBuilder->where('s.status IN (:status)')
                ->setParameter('status', $this->distinctStatuses);
        }

        $planOccurrences = $queryBuilder->getQuery()->getResult();

        $colors = ['rgb(255, 99, 132, .4)', 'rgb(75, 192, 192, .4)', 'rgb(255, 205, 86, .4)', 'rgb(201, 203, 207, .4)', 'rgb(54, 162, 235, .4)'];
        $datasets = [];
        $plans = [];
        foreach ($planOccurrences as $occurrence) {
            if (!in_array($occurrence['plan'], $plans)) {
                $plans[] = $occurrence['plan'];
                $datasets[$occurrence['plan']] = [
                    'label' => $occurrence['plan'],
                    'data' => [],
                    'fill' => false,
                    'backgroundColor' => $colors[array_search($occurrence['plan'], $plans) % count($colors)],
                    'tension' => 0.1,
                ];
            }
            $datasets[$occurrence['plan']]['data'][] = $occurrence['occurrences'];
        }

        $months = array_values(array_unique(array_column($planOccurrences, 'month')));

        return ['labels' => $months, 'datasets' => array_values($datasets),$this->distinctStatuses];
    }
    public function getStatuses(): array
        {
            return $this->SubscriptionRepository->getDistinctStatuses($this->distinctStatuses);
        }
}