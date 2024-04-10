<?php

namespace App\Form;

use App\Entity\InventoryEntry;
use App\Entity\Medication;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class InventoryEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = $builder
            ->add('quantity', options: [
                'constraints' => [
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
                'empty_data' => 1
            ]);
        /** @var InventoryEntry $data */
        $data = $builder->getData();
        if ($data?->getMedication()?->getId() === null) {
            $builder->add('medication', EntityType::class, [
                'class' => Medication::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a medication',
                'constraints' => [
                    new NotBlank(['message' => "Please select a medication"]),
                ],
            ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryEntry::class,
        ]);
    }
}
