<?php

namespace App\Form;

use App\Entity\Pharmacy;
use PixelOpen\CloudflareTurnstileBundle\Type\TurnstileType;
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

class PharmacyType extends AbstractType
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
            ->add('plainPassword', PasswordCreationType::class)
            ->add('security', TurnstileType::class, ['label' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pharmacy::class,
        ]);
    }
}
