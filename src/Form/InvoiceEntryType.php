<?php

namespace App\Form;

use App\Entity\InvoiceEntry;
use App\Entity\Medication;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class InvoiceEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('medication', EntityType::class, [
                'class' => Medication::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a medication',
                'constraints' => [
                    new NotBlank(['message' => "Please select a medication"]),
                ],
            ])
            ->add('quantity', options: [
                'attr' => ['min' => 1],
                'constraints' => [
                    new NotBlank(['message' => "Quantity cannot be blank"]),
                    new GreaterThan(['value' => 0, 'message' => "Quantity must be greater than 0"]),
                ],
                'empty_data' => 1
            ])
            ->add('cost', options: [
                'attr' => ['min' => 0.1],
                'constraints' => [
                    new NotBlank(['message' => "Cost cannot be blank"]),
                    new GreaterThan(['value' => 0, 'message' => "Cost must be greater than 0"]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceEntry::class,
        ]);
    }
}
