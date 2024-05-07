<?php

namespace App\DataFixtures;

use App\Factory\AdminFactory;
use App\Factory\DoctorFactory;
use App\Factory\InsurancePlanFactory;
use App\Factory\PatientFactory;
use App\Factory\PharmacyFactory;
use App\Factory\VisitFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        Factory::delayFlush(function () {

            // Known users

            PatientFactory::createOne([
                'firstName' => 'Anwar',
                'lastName' => 'Hussain',
                'username' => 'anwar',
            ]);

            DoctorFactory::createOne([
                'firstName' => 'Gamil',
                'lastName' => 'Arfan',
                'username' => 'gamil',
            ]);

            PharmacyFactory::createOne([
                'name' => 'Fortis Pharmacy',
                'username' => 'fortis'
            ]);

            AdminFactory::createOne([
                'username' => 'admino'
            ]);
        });
    }
}
