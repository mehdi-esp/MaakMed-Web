<?php

namespace App\Form;

use App\Entity\Medication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MedicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'constraints' => [
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'The name must be at least {{ limit }} characters long',
                        'maxMessage' => 'The name cannot be longer than {{ limit }} characters',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z][a-zA-Z0-9 .]*$/',
                        'message' => 'The name should start with a capital letter and can contain alphanumeric characters, spaces, and dots',
                    ]),
                ],
            ])
            ->add('description', null, [
                'constraints' => [
                    new Assert\Length([
                        'min' => 2,
                        'max' => 500,
                        'minMessage' => 'The description must be at least {{ limit }} characters long',
                        'maxMessage' => 'The description cannot be longer than {{ limit }} characters',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z][a-zA-Z0-9 .]*$/',
                        'message' => 'The description should start with a capital letter and can contain alphanumeric characters, spaces, and dots',
                    ]),
                ],
            ])
            ->add('insured')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Medication::class,
        ]);
    }
}