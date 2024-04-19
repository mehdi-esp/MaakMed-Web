<?php

namespace App\Form;

use App\Entity\PrescriptionEntry;
use App\Entity\Medication;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class PrescriptionEntryType extends AbstractType
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
                    new Positive(['message' => "Quantity must be greater than 0"]),
                ],
                'empty_data' => 1
            ])
            ->add('instructions', null, [
                'constraints' => [
                    new NotBlank(['message' => "Instructions cannot be blank"]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Instructions cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9\s]*$/',
                        'message' => 'Instructions should not contain special characters',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrescriptionEntry::class,
        ]);
    }
}