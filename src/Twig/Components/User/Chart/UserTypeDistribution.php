<?php

namespace App\Twig\Components\User\Chart;

use App\Repository\UserRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class UserTypeDistribution
{
    use DefaultActionTrait;

    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly UserRepository       $userRepository,
    )
    {
    }

    public function getChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT); // Changed to DOUGHNUT chart

        $data = $this->getChartData();

        $chart->setData($data);

        $chart->setOptions([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'User type distribution',
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
        $colors = [
            'ROLE_ADMIN' => 'rgb(255, 99, 132, .4)',
            'ROLE_PATIENT' => 'rgba(45, 220, 126, .4)',
            'ROLE_DOCTOR' => 'rgba(54, 162, 235, .4)',
            'ROLE_PHARMACY' => 'rgba(255, 206, 86, .4)',
        ];

        $users = $this->userRepository->findAll();

        $rolesCount = [
            'ROLE_ADMIN' => 0,
            'ROLE_PATIENT' => 0,
            'ROLE_DOCTOR' => 0,
            'ROLE_PHARMACY' => 0,
        ];

        foreach ($users as $user) {
            $roles = $user->getRoles();
            foreach ($roles as $role) {
                if (isset($rolesCount[$role])) {
                    $rolesCount[$role]++;
                }
            }
        }

        $sets = [
            [
                'label' => "User count",
                'backgroundColor' => array_map(fn($role) => $colors[$role], array_keys($rolesCount)),
                'borderColor' => array_map(fn($role) => $colors[$role], array_keys($rolesCount)),
                'data' => array_values($rolesCount),
                'tension' => 0.1,
            ]
        ];

        $roles = array_keys($rolesCount);

        return ['labels' => $roles, 'datasets' => $sets];
    }
}