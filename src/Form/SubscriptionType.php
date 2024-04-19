<?php

namespace App\Form;

use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                    'widget' => 'single_text',

                ])
                ->add('endDate', DateType::class, [
                    'widget' => 'single_text',

                ])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Pending' => 'pending',
                    'Active' => 'active',
                    'Canceled' => 'canceled',
                ],
            ])
            ->add('patient', null, [
                'disabled' => true,
                'choice_label' => 'username',
            ])
            ->add('plan', null, [
                'disabled' => true,
                'choice_label' => 'name',
            ]);
           $builder->add('endDate', DateType::class, [
               'widget' => 'single_text',
               'constraints' => [
                   new Callback([
                       'callback' => function ($endDate, ExecutionContextInterface $context) use ($builder) {
                           // Get the start date from the form data
                           $startDate = $builder->getData()->getStartDate();

                           // Create the minimum and maximum acceptable end dates
                           $minEndDate = clone $startDate;
                           $minEndDate->modify('+1 year');
                           $maxEndDate = clone $startDate;
                           $maxEndDate->modify('+1 year +1 month');

                           // Check if the end date is at least one year after the start date and at most one year and one month after the start date
                           if ($endDate < $minEndDate || $endDate > $maxEndDate) {
                               $context->buildViolation('The end date must be at least one year and at most one year and one month after the start date')
                                   ->atPath('endDate')
                                   ->addViolation();
                           }
                       },
                   ]),
               ],
           ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
