<?php

namespace App\Form\Profile;

use App\Entity\Pharmacy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;

class PharmacyP extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options: [
                'label' => ' Name',
                'attr' => [
                    'placeholder' => 'Enter your  name',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'message' => 'Your first name can only contain letters',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter your first name',
                    ]),
                ],
            ])
            ->add('username', options: [
                'label' => 'Username',
                'attr' => [
                    'placeholder' => 'Enter your  username',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\S*$/',
                        'message' => 'Your username cannot contain spaces',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter a username',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Enter your email',
                ],
                'constraints' => [
                    new Email([
                        'message' => 'The email "{{ value }}" is not a valid email.'

                    ]),

                ],
            ])
            ->add('address', options: [
                'label' => 'Address',
                'attr' => [
                    'placeholder' => 'Enter your address',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your address',
                    ]),
                ],
            ])
            ->add('number', TextType::class, [
                'label' => 'Phone Number',
                'attr' => [
                    'placeholder' => 'Enter your phone number',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\+?[1-9]\d{1,14}$/',
                        'message' => 'Your phone number must be valid',
                    ]),
                    new Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Your phone number must be exactly {{ limit }} characters long',
                    ]),

                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pharmacy::class,
        ]);
    }
}
