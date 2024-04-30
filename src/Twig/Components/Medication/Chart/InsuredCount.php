<?php

namespace App\Twig\Components\Medication\Chart;

use App\Entity\Medication;
use App\Repository\MedicationRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent('InsuredCount')]
class InsuredCount
{
    use DefaultActionTrait;

    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly MedicationRepository $medicationRepository,
    )
    {
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
                    'text' => 'Insured vs Not Insured Medications',
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
                'bar' => [
                    'borderWidth' => 1,
                ],
            ],
            'maintainAspectRatio' => false,
        ]);

        return $chart;
    }

    private function getChartData(): array
    {
        $insuredCount = $this->medicationRepository->count(['insured' => true]);
        $notInsuredCount = $this->medicationRepository->count(['insured' => false]);

        $labels = ['Insured', 'Not Insured'];
        $data = [$insuredCount, $notInsuredCount];

        $datasets = [
            [
                'label' => 'Medications',
                'backgroundColor' => ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                'borderColor' => ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                'borderWidth' => 1,
                'data' => $data,
            ],
        ];

        return ['labels' => $labels, 'datasets' => $datasets];
    }
}