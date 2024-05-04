<?php

namespace App\Twig\Components\User\Chart;

use App\Entity\Patient;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class UserAgeDistribution
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
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $data = $this->getChartData();

        $chart->setData($data);

        $chart->setOptions([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Patient age distribution',
                    'font' => [
                        'size' => 14, // Adjust the size as needed
                        'weight' => 'bold',
                    ],
                    'color' => '#FF8080', // Lighter shade of red
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
        $users = $this->userRepository->findAll();

        $userAges = array_map(function($user) {
            if ($user instanceof Patient) {
                $dob = $user->getDateOfBirth();
                $age = date('Y') - $dob->format('Y');
                return $age;
            }
        }, $users);

        $userAges = array_filter($userAges, function($value) {
            return !is_null($value);
        });

        $userAgeCounts = array_count_values($userAges);

        $labels = array_keys($userAgeCounts);
        $data = array_values($userAgeCounts);

        $colors = array_map(function() {
            return 'rgb('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).')';
        }, $labels);

        return ['labels' => $labels, 'datasets' => [['data' => $data, 'backgroundColor' => $colors]]];
    }
}