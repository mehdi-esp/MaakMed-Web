<?php

namespace App\Autocompleter;

use App\Entity\Doctor;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;

#[AutoconfigureTag('ux.entity_autocompleter', ['alias' => 'doctor'])]
class DoctorAutocompleter implements EntityAutocompleterInterface
{
    public function getEntityClass(): string
    {
        return Doctor::class;
    }

    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        return $repository
            ->createQueryBuilder('d')
            ->andWhere(
                "d.username LIKE :search OR d.nationalId LIKE :search OR CONCAT(d.firstName, ' ', d.lastName) LIKE :search OR CONCAT(d.lastName, ' ', d.firstName) LIKE :search",
            )
            ->setParameter('search', '%' . $query . '%');
    }

    /** @param Doctor $entity */
    public function getLabel(object $entity): string
    {
        return "{$entity->getFullName()} {$entity->getNationalId()} ({$entity->getSpecialization()})";
    }

    /** @param Doctor $entity */
    public function getValue(object $entity): string
    {
        return $entity->getId();
    }

    public function isGranted(Security $security): bool
    {
        // see the "security" option for details
        return true;
    }
}
