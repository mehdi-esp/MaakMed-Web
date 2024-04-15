<?php

namespace App\Form;

use App\Entity\Patient;
use PixelOpen\CloudflareTurnstileBundle\Type\TurnstileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;

class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', options: [
                'label' => 'First Name',
                'attr' => [
                    'placeholder' => 'Enter your first name',
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
            ->add('lastName', options: [
                'label' => 'Last Name',
                'attr' => [
                    'placeholder' => 'Enter your last name',
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


            ->add('dateOfBirth', DateType::class, [
                'years' => range(date('Y') - 100, date('Y')),
            ])
            ->add('nationalId', options: [
                'label' => 'NationalId',
                'attr' => [
                    'placeholder' => 'Enter your nationalId',
                ],
                'constraints' => [
                    new Length(
                        exactly: 8,
                        exactMessage: 'Your national ID must be exactly {{ limit }} characters long'
                    ),
                    new NotBlank([
                        'message' => 'Please enter your National ID',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordCreationType::class)
            ->add('security', TurnstileType::class, ['label' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
