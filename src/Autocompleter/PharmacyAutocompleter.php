<?php

namespace App\Autocompleter;

use App\Entity\Pharmacy;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;


#[AutoconfigureTag('ux.entity_autocompleter', ['alias' => 'pharmacy'])]
class PharmacyAutocompleter implements EntityAutocompleterInterface
{
    public function getEntityClass(): string
    {
        return Pharmacy::class;
    }
    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        return $repository
            ->createQueryBuilder('p')
            ->andWhere(
                "p.name LIKE :search",
            )
            ->setParameter('search', '%' . $query . '%');
    }

    /** @param Pharmacy $entity */
    public function getLabel(object $entity): string
    {
        return $entity->getName();
    }

    /** @param Pharmacy $entity */
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
