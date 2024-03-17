<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\InvoiceEntry;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
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
                ],
                // 'empty_data' => fn() => new InvoiceEntry(),
                // 'delete_empty' => true,
                // 'entry_options' => ['label' => false],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
