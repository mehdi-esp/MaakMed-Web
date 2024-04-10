<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\InvoiceEntry;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('patient', EntityType::class, [
                'class' => Patient::class,
                'choice_label' =>
                    fn(Patient $patient) => "{$patient->getFirstName()} {$patient->getLastName()} {$patient->getNationalId()}",
                'placeholder' => 'Select a patient',
                'constraints' => [
                    new NotBlank(['message' => "Please select a patient"]),
                ],
                'disabled' => $builder->getData()?->getId() !== null,
            ])
            ->add('invoiceEntries', LiveCollectionType::class, [
                'entry_type' => InvoiceEntryType::class,
                'constraints' => [
                    new Count(['min' => 1, 'minMessage' => "Please add at least one item"]),
                    new Unique([
                        'message' => 'Duplicate medication',
                        'normalizer' => fn(InvoiceEntry $entry) => $entry->getMedication()?->getId()
                    ])
                ],
                // 'empty_data' => fn() => new InvoiceEntry(),
                // 'delete_empty' => true,
                // 'entry_options' => ['label' => false],
            ]);

        // Fixes ORM/database level errors that are encountered when existing entries are reordered
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var array $data */
            $data = $event->getData();

            // Flag set in the controller which indicates that the form was user-submitted
            if (!isset($data['submitted'])) {
                return;
            }

            /** @var Invoice $invoice */
            $invoice = $form->getData();

            if (!isset($data['invoiceEntries']) && !$invoice) {
                return;
            }

            $submittedEntries = $data['invoiceEntries'];

            // Do nothing in case of duplicate entries
            $meds = array_map(
                fn(array $e) => $e['medication'],
                array_filter($submittedEntries, fn(array $e) => isset($e['medication']))
            );
            if (count($meds) !== count(array_unique($meds))) {
                return;
            }

            $data['invoiceEntries'] = [];

            $index = 0;

            foreach ($invoice->getInvoiceEntries() as $index => $entry) {
                foreach ($submittedEntries as $i => $e) {
                    if (!isset($e['medication'])) {
                        continue;
                    }
                    $medId = (int)$e['medication'];
                    if ($entry->getMedication()->getId() === $medId) {
                        $data['invoiceEntries'][$index] = $e;
                        unset($submittedEntries[$i]);
                        break;
                    }
                }
            }
            foreach ($submittedEntries as $c) {
                $data['invoiceEntries'][++$index] = $c;
            }

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
            'allow_extra_fields' => true
        ]);
    }
}
