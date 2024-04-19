<?php

namespace App\Form;

use App\Entity\InsurancePlan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class InsurancePlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
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
                        new GreaterThan([
                            'value' => 0,
                            'message' => 'The cost cannot be negative or zero',
                        ]),
                    ],
                ])
                ->add('ReimbursementRate', NumberType::class, [
                    'constraints' => [
                        new NotBlank(['message' => "Please enter the reimbursement rate"]),
                        new GreaterThan([
                            'value' => 0,
                            'message' => 'The reimbursement rate cannot be negative or zero',
                        ]),
                    ],
                ])
                ->add('Ceiling', NumberType::class, [
                    'constraints' => [
                        new NotBlank(['message' => "Please enter the ceiling"]),
                        new GreaterThan([
                            'value' => 0,
                            'message' => 'The ceiling cannot be negative or zero',
                        ]),
                    ],
                ]);
           $builder->get('Name')->addModelTransformer(new CallbackTransformer(
               function ($originalName) {
                   return $originalName;
               },
               function ($submittedName) {
                   return $submittedName === '' ? ' ' : $submittedName;
               }
           ));

           $builder->get('Description')->addModelTransformer(new CallbackTransformer(
               function ($originalDescription) {
                   return $originalDescription;
               },
               function ($submittedDescription) {
                   return $submittedDescription === '' ? ' ' : $submittedDescription;
               }
           ));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InsurancePlan::class,
            'csrf_protection' => false,
        ]);
    }
}