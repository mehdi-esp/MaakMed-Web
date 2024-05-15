<?php

namespace App\DataFixtures;

use App\Factory\AdminFactory;
use App\Factory\DoctorFactory;
use App\Factory\InsurancePlanFactory;
use App\Factory\InventoryEntryFactory;
use App\Factory\InvoiceEntryFactory;
use App\Factory\InvoiceFactory;
use App\Factory\IssueFactory;
use App\Factory\MedicationFactory;
use App\Factory\PatientFactory;
use App\Factory\PharmacyFactory;
use App\Factory\PrescriptionEntryFactory;
use App\Factory\PrescriptionFactory;
use App\Factory\SubscriptionFactory;
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


            DoctorFactory::createOne([
                'firstName' => 'Nawal',
                'lastName' => 'Bileli',
                'username' => 'nawal',
            ]);

            PharmacyFactory::createOne([
                'name' => 'Fortis',
                'username' => 'fortis'
            ]);

            AdminFactory::createOne([
                'username' => 'admino'
            ]);

            // Plans

            InsurancePlanFactory::createOne([
                'name' => 'Silver',
                'description' => 'Entry tier',
                'cost' => 200.23,
                'reimbursementRate' => 0.15,
                'ceiling' => 113.71,
            ]);

            InsurancePlanFactory::createOne([
                'name' => 'Gold',
                'description' => 'Mid tier',
                'cost' => 599.95,
                'reimbursementRate' => 0.67,
                'ceiling' => 354.53,
            ]);

            InsurancePlanFactory::createOne([
                'name' => 'Platinum',
                'description' => 'Most profitable',
                'cost' => 1799.99,
                'reimbursementRate' => 0.56,
                'ceiling' => 1199.95,
            ]);

            // Medications

            MedicationFactory::createOne(
                [
                    'name' =>  'Acetaminophen',
                    'description' =>  'Acetaminophen also known as paracetamol is a common over the counter medication used to relieve pain and reduce fever. It works by inhibiting the production of prostaglandins in the brain  which are chemicals involved in pain perception and the regulation of body temperature. Acetaminophen is often used to alleviate headaches muscle aches arthritis pain  and discomfort associated with colds and fevers.',
                ]
            );
            MedicationFactory::createOne(
                [
                    'name' => 'Adderalll',
                    'description' =>   'Adderall is a prescription medication used to treat attention deficit hyperactivity disorder  and narcolepsy. It contains a combination of amphetamine and dextroamphetamine which work by increasing the levels of certain neurotransmitters in the brain improving focus attention and impulse control. Common side effects include insomnia loss of appetite and increased heart rate. It can be habit forming and should be used cautiousl',
                ]
            );
            MedicationFactory::createOne(
                [
                    'name' => 'Ativan100mg',
                    'description' => 'Ativan is a benzodiazepine medication used to treat anxiety disorders and short term relief of symptoms. It works by enhancing the effects of a neurotransmitter in the brain producing a calming effect. Side effects may include drowsiness dizziness and confusion. It s typically prescribed for short term use due to the risk of dependence and withdrawal symptoms',
                ]
            );
            MedicationFactory::createOne(
                [
                    'name' => 'Amoxicillin',
                    'description' => 'Amoxicillin is an antibiotic used to treat bacterial infections like ear infections pneumonia  and strep throat. It works by stopping bacteria from multiplying. Side effects may include nausea diarrhea and rash. Finish the full course as directed by your doctor to prevent antibiotic resistance',
                ]
            );
            MedicationFactory::createOne(
                [
                    'name' =>  'Coreg',
                    'description' => 'Coreg is a medication for heart failure and high blood pressure. It belongs to a class of drugs called beta blockers which work by relaxing blood vessels and slowing heart rate to improve blood flow and reduce blood pressure. Side effects may include dizziness fatigue and low blood pressure. Take as prescribed by your doctor and monitor for any signs of worsening heart failure or side effects',
                ]
            );

            MedicationFactory::createOne(
                [
                    'name' => 'Panadol',
                    'description' => 'Panadol is a brand name for acetaminophen a common over the counter medication used to relieve pain and reduce fever. It works by inhibiting the production of prostaglandins in the brain  which are chemicals involved in pain perception and the regulation of body temperature. Panadol is often used to alleviate headaches muscle aches arthritis pain  and discomfort associated with colds and fevers.',
                ]
            );

            MedicationFactory::createOne(
                [
                    'name' => 'Ibuprofen',
                    'description' => 'Ibuprofen is a nonsteroidal anti inflammatory drug NSAID used to treat pain and reduce fever. It works by blocking the production of prostaglandins in the body which are chemicals involved in pain inflammation and fever. Common side effects include stomach upset and ulcers. Take with food or milk to reduce stomach irritation',

                ]
            );

            MedicationFactory::createOne(
                [
                    'name' => 'Phenylephrine',
                    'description' => 'Phenylephrine is a decongestant used to relieve nasal congestion due to colds allergies and sinus infections. It works by constricting blood vessels in the nasal passages to reduce swelling and congestion. Side effects may include headache dizziness and nervousness. It should be used cautiously in patients with high blood pressure and heart disease.'
                ]
            );
        });

        // Used medications

        $meds = [
            'Acetaminophen',
            'Adderalll',
            'Ativan100mg',
            'Amoxicillin',
            'Coreg',
            'Panadol',
            'Ibuprofen',
            'Phenylephrine',
        ];

        // One invoice for the fortis pharmacy

        $invoice = InvoiceFactory::createOne([
            'pharmacy' => PharmacyFactory::find(['username' => 'fortis']),
            'invoiceEntries' => InvoiceEntryFactory::randomRange(0, 0),
        ]);

        Factory::delayFlush(function () use ($meds, $invoice) {
            foreach (array_rand(array_flip($meds), rand(intdiv(count($meds), 2), count($meds))) as $medName) {
                InvoiceEntryFactory::createOne([
                    'invoice' => $invoice,
                    'medication' => MedicationFactory::findOrCreate(['name' => $medName]),
                ]);
            }
        });


        // Subscriptions and patients

        SubscriptionFactory::createMany(20, fn () => [
            'patient' => PatientFactory::createOne(),
            'status' => 'active'
        ]);

        SubscriptionFactory::createMany(10, ['status' => 'canceled']);


        // Visits

        Factory::delayFlush(function () {

            VisitFactory::createOne([
                'doctor' => DoctorFactory::find(['username' => 'gamil']),
                'patient' => PatientFactory::find(['username' => 'anwar']),
                'diagnosis' => 'Patient has fever, their temperature is 38.5C.',
            ]);

            VisitFactory::createMany(5, [
                'patient' => PatientFactory::find(['username' => 'anwar']),
            ]);

            VisitFactory::createMany(5, [
                'doctor' => DoctorFactory::find(['username' => 'gamil']),
                'patient' => PatientFactory::new()
            ]);

            VisitFactory::createMany(5, [
                'doctor' => DoctorFactory::find(['username' => 'nawal']),
                'patient' => PatientFactory::new()
            ]);

            VisitFactory::createMany(40);
        });

        // Prescriptions

        $prescription = PrescriptionFactory::createOne([
            'medications' => PrescriptionEntryFactory::randomRange(0, 0),
        ]);

        Factory::delayFlush(function () use ($meds, $prescription) {
            $instructions = [
                'Take one pill every 6 hours',
                'Take with food',
                'Take with a full glass of water',
                'Take two pills every 8 hours',
                'Take one pill every 12 hours',
            ];

            foreach (array_rand(array_flip($meds), rand(2, count($meds))) as $medName) {
                PrescriptionEntryFactory::createOne([
                    'prescription' => $prescription,
                    'medication' => MedicationFactory::findOrCreate(['name' => $medName]),
                    'instructions' => $instructions[array_rand($instructions)],
                ]);
            }
        });

        // Inventory

        Factory::delayFlush(function () use ($meds) {
            foreach ($meds as $medName) {
                InventoryEntryFactory::createOne([
                    'medication' => MedicationFactory::findOrCreate(['name' => $medName]),
                    'pharmacy' => PharmacyFactory::find(['username' => 'fortis']),
                ]);
            }
        });

        // Issues

        Factory::delayFlush(function () {
            IssueFactory::createOne(
                [
                    'title' => 'problem',
                    'content' => "I can't add an issue",
                    'creationDate' => new \DateTimeImmutable('-1 days'),
                    'category' => 'Other'
                ]
            );

            IssueFactory::createOne(
                [
                    'title' => 'payment',
                    'content' => "payment page is still loading",
                    'creationDate' => new \DateTimeImmutable('-3 days'),
                    'category' => 'Other'
                ]
            );

            IssueFactory::createOne([
                'title' => 'late',
                'content' => 'The doctor was late to our appointment',
                'category' => 'Doctor'
            ]);

            IssueFactory::createOne([
                'title' => 'Medication',
                'content' => 'The pharmacist gave me the wrong amount of medication',
                'creationDate' => new \DateTimeImmutable('-3 days'),
                'category' => 'Pharmacy'
            ]);

            IssueFactory::createOne(
                [
                    'title' => 'profile',
                    'content' => "I cant display my profile page",
                    'creationDate' => new \DateTimeImmutable('-10 days'),
                    'category' => 'Medication'

                ]
            );

            IssueFactory::createOne(
                [
                    'title' => 'cancel',
                    'content' => "I can't cancel my subscription",
                    'creationDate' => new \DateTimeImmutable('-7 days'),
                    'category' => 'Medication'

                ]
            );

            IssueFactory::createOne(
                [
                    'title' => 'error',
                    'content' => "I can't display the user page",
                    'creationDate' => new \DateTimeImmutable('-4 days'),
                    'category' => 'Other'
                ]
            );

            IssueFactory::createOne(
                [
                    'title' => 'inventory',
                    'content' => "I can't add a medication to my inventory",
                    'creationDate' => new \DateTimeImmutable('-2 days'),
                    'category' => 'Pharmacy'
                ]
            );
        });
    }
}
