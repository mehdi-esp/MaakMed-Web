<?php

namespace App\Twig\Components\Visit\Chart;

use App\Entity\VisitCategory;
use App\Repository\VisitRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class TypeDistribution
{
    use DefaultActionTrait;

    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly VisitRepository       $visitRepository,
    )
    {
    }

    public function getChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_PIE);

        $data = $this->getChartData();

        $chart->setData($data);

        $chart->setOptions([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Visit distribution',
                ],
            ],
        ]);

        return $chart;
    }

    private function getChartData(): array
    {
        $colors = [
            VisitCategory::ILLNESS->value => 'rgb(255, 99, 132, .4)',
            VisitCategory::FOLLOW_UP->value => 'rgba(45, 220, 126, .4)',
            VisitCategory::SPECIALIST->value => 'rgba(54, 162, 235, .4)',
            VisitCategory::CHECK_UP->value => 'rgba(255, 206, 86, .4)',
        ];
        $results = $this->visitRepository->createQueryBuilder('v')
            ->select('v.type, COUNT(v.id) visit_count')
            ->groupBy('v.type')
            ->getQuery()
            ->getResult();
        $transformed = array_reduce(
            $results,
            function (array $carry, array $result) {
                $carry[$result['type']->value] = $result['visit_count'];
                return $carry;
            },
            []
        );
        $sets = [
            [
                'label' => "Visit count",
                'backgroundColor' => array_map(fn($type) => $colors[$type], array_keys($transformed)),
                'borderColor' => array_map(fn($type) => $colors[$type], array_keys($transformed)),
                'data' => array_values($transformed),
                'tension' => 0.1,
            ]
        ];

        $types = array_map(
            fn(string $category) => VisitCategory::from($category)->getDisplayName(),
            array_keys($transformed)
        );

        return ['labels' => $types, 'datasets' => $sets];
    }
}
