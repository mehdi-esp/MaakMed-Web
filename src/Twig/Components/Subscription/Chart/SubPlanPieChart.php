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
class SubPlanPieChart
{
     use DefaultActionTrait;


    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly SubscriptionRepository  $SubscriptionRepository,
        private readonly InsurancePlanRepository  $InsurancePlanRepository,
    )
    {
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
      private function generateRandomColor(): string {
                   $red = mt_rand(0, 255);
                   $green = mt_rand(0, 255);
                   $blue = mt_rand(0, 255);
                   $color = "rgb($red, $green, $blue)";
                   return $color;
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
    }
