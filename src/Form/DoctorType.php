<?php

namespace App\Form;

use App\Entity\Doctor;
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

class DoctorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('FirstName', TextType::class, [
                'label' => 'First Name',
                'attr' => [
                    'placeholder' => 'Enter your first name',
                    'class' => 'form-control mb-3 rounded-lg',
                    'style' => 'border-color: #086475;'
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
            ->add('LastName', TextType::class, [
                'label' => 'Last Name',
                'attr' => [
                    'placeholder' => 'Enter your last name',
                    'class' => 'form-control mb-3 rounded-lg',
                    'style' => 'border-color: #086475;'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'message' => 'Your last name can only contain letters',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter your last name',
                    ]),
                ],
            ])
            ->add('Username', TextType::class, [

                'attr' => [
                    'placeholder' => 'Enter your  username',
                    'class' => 'form-control mb-3 rounded-lg',
                    'style' => 'border-color: #086475;'
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


            ->add('Specialization', TextType::class, [
                'label' => 'Specialization',
                'attr' => [
                    'placeholder' => 'Enter your specialization',
                    'class' => 'form-control mb-3 rounded-lg',
                    'style' => 'border-color: #086475;'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'message' => 'Your last name can only contain letters',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter your specialization',
                    ]),
                ],
            ])
            ->add('NationalId', TextType::class, [
                'label' => 'NationalId',
                'attr' => [
                    'placeholder' => 'Enter your nationalId',
                    'class' => 'form-control mb-3 rounded-lg',
                    'style' => 'border-color: #086475;'
                ],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Your national ID must be exactly {{ limit }} characters long',
                    ]),
                    new NotBlank([
                        'message' => 'Please enter your National ID',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]*$/',
                        'message' => 'Your national ID can only contain numbers',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password',

                    'attr' => [
                        'id' => 'password',
                        'placeholder' => 'Enter your password',
                        'class' => 'form-control mb-3 rounded-lg', // Added 'rounded-lg' class
                        'style' => 'border-color: #086475;'
                    ],

                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr' => [
                        'placeholder' => 'Repeat your password',
                        'class' => 'form-control mb-3 rounded-lg', // Added 'rounded-lg' class
                        'style' => 'border-color: #086475;'
                    ],
                ],
                'invalid_message' => 'The password fields must match.',
                'attr' => [
                    'placeholder' => 'Enter your password',
                    'class' => 'form-control mb-3'
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                        'message' => 'Your password should contain at least one uppercase letter, one number, and one special character',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Register',
                'attr' => [
                    'class' => 'btn  btn-primary',
                    'style' => 'background-color: #086475; border-color: #086475;'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Doctor::class,
        ]);
    }
}
