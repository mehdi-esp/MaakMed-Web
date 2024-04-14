<?php

namespace App\Form;

use App\Entity\Prescription;
use App\Entity\PrescriptionEntry;
use App\Entity\Visit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Security;

class PrescriptionType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('visit', EntityType::class, [
                'class' => Visit::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('v')
                        ->leftJoin('v.prescription', 'p')
                        ->where('p.id IS NULL')
                        ->andWhere('v.doctor = :doctor')
                        ->setParameter('doctor', $this->security->getUser());
                },
                'choice_label' => function (?Visit $visit) {
                    return $visit ? $visit->getPatient()->getUsername() : 'All visits have a prescription';
                },
                'placeholder' => 'Select a visit',
                'constraints' => [
                    new NotBlank(['message' => "Please select a visit"]),
                ],
                'disabled' => $builder->getData()?->getId() !== null,
            ])
            ->add('medications', LiveCollectionType::class, [
                'entry_type' => PrescriptionEntryType::class,
                'constraints' => [
                    new Count(['min' => 1, 'minMessage' => "Please add at least one item"]),
                    new Unique([
                        'message' => 'Duplicate medication',
                        'normalizer' => fn(PrescriptionEntry $entry) => $entry->getMedication()?->getId()
                    ])
                ],
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (!isset($data['submitted'])) {
                return;
            }

            $prescription = $form->getData();

            if (!isset($data['medications']) && !$prescription) {
                return;
            }

            $submittedEntries = $data['medications'];
            $meds = array_map(
                fn(array $e) => $e['medication'],
                array_filter($submittedEntries, fn(array $e) => isset($e['medication']))
            );
            if (count($meds) !== count(array_unique($meds))) {
                return;
            }

            $data['medications'] = [];
            $index = 0;

            foreach ($prescription->getMedications() as $index => $entry) {
                foreach ($submittedEntries as $i => $e) {
                    if (!isset($e['medication'])) {
                        continue;
                    }
                    $medId = (int)$e['medication'];
                    if ($entry->getMedication()->getId() === $medId) {
                        $data['medications'][$index] = $e;
                        unset($submittedEntries[$i]);
                        break;
                    }
                }
            }
            foreach ($submittedEntries as $c) {
                $data['medications'][++$index] = $c;
            }

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prescription::class,
            'allow_extra_fields' => true
        ]);
    }
}