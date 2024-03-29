<?php

namespace App\Autocompleter;

use App\Entity\Patient;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;

#[AutoconfigureTag('ux.entity_autocompleter', ['alias' => 'patient'])]
class PatientAutocompleter implements EntityAutocompleterInterface
{
    public function getEntityClass(): string
    {
        return Patient::class;
    }

    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        return $repository
            ->createQueryBuilder('p')
            ->andWhere(
                "p.username LIKE :search OR p.nationalId LIKE :search OR CONCAT(p.firstName, ' ', p.lastName) LIKE :search OR CONCAT(p.lastName, ' ', p.firstName) LIKE :search",
            )
            ->setParameter('search', '%' . $query . '%');
    }

    /** @param Patient $entity */
    public function getLabel(object $entity): string
    {
        return "{$entity->getFullName()} {$entity->getNationalId()}";
    }

    /** @param Patient $entity */
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
