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

        // One invoice

        $meds = ['Panadol', 'Phenylephrine', 'Ibuprofen', 'Coreg', 'Amoxicillin', 'Ativan100mg', 'Adderalll'];

        $invoice = InvoiceFactory::createOne([
            'pharmacy' => PharmacyFactory::find(['username' => 'fortis']),
            'invoiceEntries' => InvoiceEntryFactory::randomRange(0, 0),
        ]);

        InvoiceEntryFactory::createOne([
            'invoice' => $invoice,
            'medication' => MedicationFactory::findOrCreate(['name' => 'Panadol']),
        ]);

        InvoiceEntryFactory::createOne([
            'invoice' => $invoice,
            'medication' => MedicationFactory::findOrCreate(['name' => 'Phenylephrine']),
        ]);

        InvoiceEntryFactory::createOne([
            'invoice' => $invoice,
            'medication' => MedicationFactory::findOrCreate(['name' => 'Ibuprofen']),
        ]);

        Factory::delayFlush(function () {
            VisitFactory::createMany(5, [
                'doctor' => DoctorFactory::find(['username' => 'gamil']),
                'patient' => PatientFactory::find(['username' => 'anwar']),
            ]);
            VisitFactory::createMany(5, [
                'doctor' => DoctorFactory::find(['username' => 'gamil']),
            ]);
            VisitFactory::createMany(5, [
                'patient' => PatientFactory::find(['username' => 'anwar']),
            ]);
        });

        // Prescriptions

        $prescription = PrescriptionFactory::createOne([
            'medications' => PrescriptionEntryFactory::randomRange(0, 0),
        ]);

        foreach ($meds as $medName) {
            PrescriptionEntryFactory::createOne([
                'prescription' => $prescription,
                'medication' => MedicationFactory::findOrCreate(['name' => $medName]),
            ]);
        }

        // Subscriptions
        SubscriptionFactory::createMany(20, fn () => [
            'patient' => PatientFactory::createOne(),
            'status' => 'active'
        ]);

        SubscriptionFactory::createMany(10, ['status' => 'canceled']);

        // Inventory

        foreach ($meds as $medName) {
            InventoryEntryFactory::createOne([
                'medication' => MedicationFactory::findOrCreate(['name' => $medName]),
                'pharmacy' => PharmacyFactory::find(['username' => 'fortis']),
            ]);
        }
    }
}
