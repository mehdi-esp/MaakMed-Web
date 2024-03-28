<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Sequentially;

class PasswordCreationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'type' => PasswordType::class,
            'first_options' => [
                'label' => 'Password',
                'attr' => [
                    'placeholder' => 'Enter your password',
                ],
            ],
            'second_options' => [
                'label' => 'Repeat Password',
                'attr' => [
                    'placeholder' => 'Repeat your password',
                ],
            ],
            'invalid_message' => 'The password fields must match.',
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
                new Sequentially(
                    [
                        new Regex([
                            'pattern' => '/[a-z]+/',
                            'message' => 'Your password should contain at least one lowercase letter',
                        ]),
                        new Regex([
                            'pattern' => '/[A-Z]+/',
                            'message' => 'Your password should contain at least one uppercase letter',
                        ]),
                        new Regex([
                            'pattern' => '/\d+/',
                            'message' => 'Your password should contain at least one number',
                        ]),
                        new Regex([
                            'pattern' => '/\W+/',
                            'message' => 'Your password should contain at least one special character',
                        ]),
                    ]
                ),
            ],
        ]);
    }

    public function getParent(): string
    {
        return RepeatedType::class;
    }
}
