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
use DateTime;

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


    public function getChart(): Chart {
            $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

            $data = $this->getChartData();
            $chart->setData($data);

            $chart->setOptions([
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'precision' => 0
                        ]
                    ]
                ],
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Subscribers X Plans Distribution',
                    ],
                    'legend' => [
                        'display' => true
                    ]
                ],
                'responsive' => true
            ]);

            return $chart;
        }

        private function getChartData(): array {
            $queryBuilder = $this->SubscriptionRepository->createQueryBuilder('s')
                ->join('s.plan', 'p')
                ->select('p.name as plan, SUBSTRING(s.startDate, 6, 2) as month, COUNT(s.id) as occurrences')
                ->groupBy('p.name, month')
                ->orderBy('month', 'ASC');
            if (!empty($this->distinctStatuses)) {
                $queryBuilder->where('s.status IN (:status)')
                    ->setParameter('status', $this->distinctStatuses);
            }

            $planOccurrences = $queryBuilder->getQuery()->getResult();

            $colors = ['rgb(255, 99, 132, .4)', 'rgb(75, 192, 192, .4)', 'rgb(255, 205, 86, .4)', 'rgb(201, 203, 207, .4)', 'rgb(54, 162, 235, .4)'];
            $datasets = [];
            $plans = [];
            $monthsIndex = array_fill_keys(range(1, 12), 0); // Prepare array with keys from 1 to 12

            foreach ($planOccurrences as $occurrence) {
                $monthFormatted = sprintf('%02d', $occurrence['month']); // Ensure month is always two digits
                if (!isset($datasets[$occurrence['plan']])) {
                    $plans[] = $occurrence['plan'];
                    $datasets[$occurrence['plan']] = [
                        'label' => $occurrence['plan'],
                        'data' => array_values($monthsIndex), // Clone the array to ensure separate data for each dataset
                        'backgroundColor' => $colors[array_search($occurrence['plan'], $plans) % count($colors)],
                        'tension' => 0.1
                    ];
                }
                $datasets[$occurrence['plan']]['data'][((int)$monthFormatted) - 1] = (int)$occurrence['occurrences']; // Adjust index for 0-based array
            }

            $months = array_map(function ($month) {
                return DateTime::createFromFormat('m', $month)->format('F');
            }, array_keys($monthsIndex));

            return ['labels' => $months, 'datasets' => array_values($datasets)];
        }
    public function getPieChart(): Chart {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_PIE);

        $data = $this->getPieChartData();
        $chart->setData($data);

        $chart->setOptions([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Subscribers Distribution by Plans',
                ],
                'legend' => [
                    'display' => true
                ]
            ],
            'responsive' => true
        ]);

        return $chart;
    }

   private function getPieChartData(): array {
       $queryBuilder = $this->SubscriptionRepository->createQueryBuilder('s')
           ->join('s.plan', 'p')
           ->select('p.name as plan, COUNT(s.id) as subscribers')
           ->groupBy('p.name');

       if (!empty($this->distinctStatuses)) {
           $queryBuilder->where('s.status IN (:status)')
               ->setParameter('status', $this->distinctStatuses);
       }

       $planSubscribers = $queryBuilder->getQuery()->getResult();

       $labels = [];
       $data = [];
       foreach ($planSubscribers as $planSubscriber) {
           $labels[] = $planSubscriber['plan'];
           $data[] = (int)$planSubscriber['subscribers'];
       }

       return [
           'labels' => $labels,
           'datasets' => [
               [
                   'label' => 'Subscribers by Plan',
                   'data' => $data,
                   'backgroundColor' => array_map(function() {
                       return $this->generateRandomColor();
                   }, $labels),
                   'hoverOffset' => 4
               ]
           ]
       ];
   }


    private function generateRandomColor(): string {
        // Generate a random RGB color value
        $red = mt_rand(0, 255);
        $green = mt_rand(0, 255);
        $blue = mt_rand(0, 255);

        // Format the RGB values into a CSS color string
        $color = "rgb($red, $green, $blue)";

        return $color;
    }

    public function getStatuses(): array
        {
            return $this->SubscriptionRepository->getDistinctStatuses($this->distinctStatuses);
        }
}