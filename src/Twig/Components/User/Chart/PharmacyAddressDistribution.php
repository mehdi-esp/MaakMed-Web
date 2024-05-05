<?php

namespace App\Twig\Components\User\Chart;

use App\Repository\PharmacyRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class PharmacyAddressDistribution
{
    use DefaultActionTrait;

    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly PharmacyRepository    $pharmacyRepository,
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
                    'text' => 'Pharmacy address distribution',
                    'font' => [
                        'size' => 14    , // Adjust the size as needed
                        'weight' => 'bold', // Make the title bold
                    ],
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'font' => [
                            'size' => 14,
                            'color' => '#000',
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.7)',
                    'titleFont' => [
                        'size' => 12,
                        'color' => '#fff',
                    ],
                    'bodyFont' => [
                        'size' => 10,
                        'color' => '#fff',
                    ],
                ],
            ],
        ]);

        return $chart;
    }
    private function getChartData(): array
    {
        $distribution = $this->pharmacyRepository->getPharmacyAddressDistribution();

        $labels = array_column($distribution, 'address');
        $data = array_column($distribution, 'count');

        $backgroundColor = array_map(function () {
            return 'rgba(' . rand(100, 255) . ', ' . rand(100, 255) . ', ' . rand(100, 255) . ', 0.7)';
        }, $data);

        $borderColor = array_map(function () {
            return 'rgba(' . rand(100, 255) . ', ' . rand(100, 255) . ', ' . rand(100, 255) . ', 1)';
        }, $data);

        $sets = [
            [
                'label' => "Address count",
                'backgroundColor' => $backgroundColor,
                'borderColor' => $borderColor,
                'data' => $data,
                'tension' => 0.1,
            ]
        ];

        return ['labels' => $labels, 'datasets' => $sets];
    }
}