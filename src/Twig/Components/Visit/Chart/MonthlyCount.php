<?php

namespace App\Twig\Components\Visit\Chart;

use App\Entity\VisitCategory;
use App\Repository\VisitRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class MonthlyCount
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    /** @var VisitCategory[] $currentTypes */
    public array $currentTypes;

    #[LiveProp(writable: true)]
    public int $year;

    #[LiveProp(writable: true)]
    public bool $categorize = true;

    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly VisitRepository       $visitRepository,
    )
    {
        $this->currentTypes = $this->allTypes();
        $this->year = (int)date('Y');
    }

    #[ExposeInTemplate]
    public function getChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $data = $this->getChartData();

        $chart->setData($data);

        $chart->setOptions([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Visit count ' . $this->year,
                ],
                'legend' => [
                    'labels' => [
                        'boxHeight' => 20,
                        'boxWidth' => 50,
                        'padding' => 20,
                        'font' => [
                            'size' => 14,
                        ],
                    ],
                ],
            ],
            'elements' => [
                'line' => [
                    'borderWidth' => 5,
                    'tension' => 0.25,
                    'borderCapStyle' => 'round',
                    'borderJoinStyle' => 'round',
                ],
            ],
            'maintainAspectRatio' => false,
        ]);

        return $chart;
    }

    #[ExposeInTemplate]
    public function allTypes(): array
    {
        return VisitCategory::cases();
    }

    private function getChartData(): array
    {

        $maxMonth = $this->year === (int)date('Y') ? (int)date('m') : 12;

        $monthRangeSoFar = range(1, $maxMonth);

        $months = array_map(
            fn(int $month) => \DateTime::createFromFormat('!m', $month)->format('F'),
            range(1, 12)
        );

        $qb = $this->visitRepository->createQueryBuilder('v')
            ->select("v.type, MONTH(v.date) month, COUNT(v.id) as visit_count")
            ->where('year(v.date) = :year')
            ->setParameter('year', $this->year);

        if (count($this->currentTypes) !== 0) {
            $qb->andWhere('v.type IN (:types)')
                ->setParameter('types', $this->currentTypes);
        }

        if (!$this->categorize) {
            $qb->groupBy('month');
            $results = $qb->getQuery()->getResult();

            $transformed = array_reduce($results, function (array $result, array $group) {
                $month = $group['month'];
                $result[$month] = $group['visit_count'];
                return $result;
            }, array_fill_keys($monthRangeSoFar, 0));
            $sets = [
                [
                    'label' => 'Visits',
                    'backgroundColor' => 'rgba(54, 162, 235, .4)',
                    'borderColor' => 'rgba(54, 162, 235, .4)',
                    'data' => array_values($transformed),
                    'tension' => 0.1,
                ],
            ];
        } else {
            $qb->groupBy('v.type')
                ->addGroupBy('month');
            $results = $qb->getQuery()->getResult();

            $colors = [
                VisitCategory::ILLNESS->value => 'rgb(255, 99, 132, .4)',
                VisitCategory::FOLLOW_UP->value => 'rgba(45, 220, 126, .4)',
                VisitCategory::SPECIALIST->value => 'rgba(54, 162, 235, .4)',
                VisitCategory::CHECK_UP->value => 'rgba(255, 206, 86, .4)',
            ];

            $transformed = array_reduce($results, function (array $result, array $group) {
                $type = $group['type']->value;
                $month = $group['month'];
                $result[$type][$month] = $group['visit_count'];
                return $result;
            }, array_fill_keys(
                array_map(fn(VisitCategory $type) => $type->value, $this->currentTypes ?: $this->allTypes()),
                array_fill_keys($monthRangeSoFar, 0)
            ));

            $sets = array_map(
                fn(string $type, array $counts) => [
                    'label' => VisitCategory::from($type)->getDisplayName(),
                    'backgroundColor' => $colors[$type],
                    'borderColor' => $colors[$type],
                    'data' => array_values($counts),
                    'tension' => 0.1,
                ],
                array_keys($transformed),
                array_values($transformed)
            );

        }
        return ['labels' => $months, 'datasets' => $sets];
    }
}
