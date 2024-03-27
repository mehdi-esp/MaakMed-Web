<?php

namespace App\Form;

use App\Entity\InsurancePlan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class InsurancePlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => "Please write the name of your plan"]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9 ]*$/',
                        'message' => 'The name can only contain letters, digits, and spaces',
                    ]),
                    new NotBlank([
                        'message' => 'The name cannot be only spaces',
                        'normalizer' => 'trim',
                    ]),
                ],
            ])
            ->add('Description', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => "Please add a description"]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9 ]*$/',
                        'message' => 'The description can only contain letters, digits, and spaces',
                    ]),
                    new NotBlank([
                        'message' => 'The description cannot be only spaces',
                        'normalizer' => 'trim',
                    ]),
                ],
            ])
            ->add('Cost', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => "Please enter the cost"]),
                ],
            ])
            ->add('ReimbursementRate', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => "Please enter the reimbursement rate"]),
                ],
            ])
            ->add('Ceiling', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => "Please enter the ceiling"]),
                ],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InsurancePlan::class,
            'csrf_protection' => false,
        ]);
    }
}