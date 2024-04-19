<?php

namespace App\Form;

use App\Entity\Issue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class IssueUpdateType  extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 10]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9 ]*$/',
                        'message' => 'Only letters, numbers and spaces are allowed',
                    ]),
                ],
            ])
            ->add('title', null, [
                'disabled' => true,
            ])
            ->add('category', null, [
                'disabled' => true,
            ]);
           /* ->add('creationDate', null, [
                'disabled' => true,
            ]); */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Issue::class,
        ]);
    }
}