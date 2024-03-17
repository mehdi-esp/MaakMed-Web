<?php

namespace App\Form;

use App\Entity\Patient;
use App\Entity\Visit;
use App\Entity\VisitCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class VisitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('patient', EntityType::class, [
                'class' => Patient::class,
                'choice_label' =>
                    fn(Patient $patient) => "{$patient->getFirstName()} {$patient->getFirstName()} {$patient->getLastName()}",
                'placeholder' => 'Select a patient',
                'constraints' => [
                    new NotBlank(['message' => "Please select a patient"]),
                ],
                'disabled' => $builder->getData()?->getId() !== null,
            ])
            ->add('type', EnumType::class, [
                'class' => VisitCategory::class,
                'choice_label' => 'displayName',
                'constraints' => [new NotBlank(['message' => 'Please select a visit type.']),]
            ])
            ->add(
                'diagnosis',
                options: [
                    'constraints' => [
                        new NotBlank(['message' => "Diagnosis cannot be blank"]),
                        new Regex([
                            'pattern' => '/^[A-Z]/',
                            'message' => "Diagnosis must start with a capital letter",
                        ]),
                        new Regex([
                            'pattern' => '/\.$/',
                            'message' => "Diagnosis must end with a period",
                        ]),
                    ],
                    'help' => "Diagnosis must start with a capital letter and end with a period",
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Visit::class,
        ]);
    }
}
