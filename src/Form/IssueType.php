<?php

namespace App\Form;

use App\Entity\Issue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // Correct import statement for ChoiceType

class IssueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
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
