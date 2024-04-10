<?php

namespace App\Form;

use App\Entity\InventoryEntry;
use App\Entity\Pharmacy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class InventoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('inventoryEntries', LiveCollectionType::class, [
                'entry_type' => InventoryEntryType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'constraints' => [
                    new Unique(
                        message: 'Duplicate medication',
                        normalizer: fn(InventoryEntry $entry) => $entry->getMedication()?->getId(),
                    )
                ]
            ]);

        // Fixes ORM/database level errors that are encountered when existing entries are reordered
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var array $data */
            $data = $event->getData();

            // Flag set in the controller which indicates that the form was user-submitted
            if (!isset($data['submitted'])) {
                return;
            }

            /** @var Pharmacy $pharmacy */
            $pharmacy = $form->getData();

            if (!isset($data['inventoryEntries']) && !$pharmacy) {
                return;
            }

            $submittedEntries = $data['inventoryEntries'];

            // Do nothing in case of duplicate entries
            $meds = array_map(
                fn(array $e) => $e['medication'],
                array_filter($submittedEntries, fn(array $e) => isset($e['medication']))
            );
            if (count($meds) !== count(array_unique($meds))) {
                return;
            }

            $data['inventoryEntries'] = [];

            $index = 0;

            foreach ($pharmacy->getInventoryEntries() as $index => $entry) {
                foreach ($submittedEntries as $i => $e) {
                    if (!isset($e['medication'])) {
                        continue;
                    }
                    $medId = (int)$e['medication'];
                    if ($entry->getMedication()->getId() === $medId) {
                        $data['inventoryEntries'][$index] = $e;
                        unset($submittedEntries[$i]);
                        break;
                    }
                }
            }
            foreach ($submittedEntries as $c) {
                $data['inventoryEntries'][++$index] = $c;
            }

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pharmacy::class,
            'allow_extra_fields' => true
        ]);
    }
}
