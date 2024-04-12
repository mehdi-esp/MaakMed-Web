<?php

namespace App\Form;

use App\Entity\Issue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Regex;

// Correct import statement for ChoiceType

class IssueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class, [
            'constraints' => [
                new NotBlank(),
                new Length(['max' => 10]),
                new Regex([
                    'pattern' => '/^[a-zA-Z0-9 ]*$/',
                    'message' => 'Only letters, numbers and spaces are allowed',
                ]),
            ],
        ])
            ->add('content', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('category', ChoiceType::class, [ // Use ChoiceType from the correct namespace
                'choices' => [
                    'Pharmacy' => 'Pharmacy',
                    'Medication' => 'Medication',
                    'Doctor' => 'Doctor',
                    'Other' => 'Other',],
                ])

            //->add('creationDate')
            ;
}


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Issue::class,
        ]);
    }

}
