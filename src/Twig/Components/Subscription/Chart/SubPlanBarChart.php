<?php

namespace App\Twig\Components\Subscription\Chart;

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
class SubPlanBarChart
{
     use DefaultActionTrait;

    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly SubscriptionRepository  $SubscriptionRepository,
        private readonly InsurancePlanRepository  $InsurancePlanRepository,
    )
    {
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
            $monthsIndex = array_fill_keys(range(1, 12), 0);

            foreach ($planOccurrences as $occurrence) {
                $monthFormatted = sprintf('%02d', $occurrence['month']);
                if (!isset($datasets[$occurrence['plan']])) {
                    $plans[] = $occurrence['plan'];
                    $datasets[$occurrence['plan']] = [
                        'label' => $occurrence['plan'],
                        'data' => array_values($monthsIndex),
                        'backgroundColor' => $colors[array_search($occurrence['plan'], $plans) % count($colors)],
                        'tension' => 0.1
                    ];
                }
                $datasets[$occurrence['plan']]['data'][((int)$monthFormatted) - 1] = (int)$occurrence['occurrences'];
            }

            $months = array_map(function ($month) {
                return DateTime::createFromFormat('m', $month)->format('F');
            }, array_keys($monthsIndex));

            return ['labels' => $months, 'datasets' => array_values($datasets)];
        }

}