<?php

namespace App\Factory;

use App\Entity\Prescription;
use App\Repository\PrescriptionRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Prescription>
 *
 * @method        Prescription|Proxy create(array|callable $attributes = [])
 * @method static Prescription|Proxy createOne(array $attributes = [])
 * @method static Prescription|Proxy find(object|array|mixed $criteria)
 * @method static Prescription|Proxy findOrCreate(array $attributes)
 * @method static Prescription|Proxy first(string $sortedField = 'id')
 * @method static Prescription|Proxy last(string $sortedField = 'id')
 * @method static Prescription|Proxy random(array $attributes = [])
 * @method static Prescription|Proxy randomOrCreate(array $attributes = [])
 * @method static PrescriptionRepository|RepositoryProxy repository()
 * @method static Prescription[]|Proxy[] all()
 * @method static Prescription[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Prescription[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Prescription[]|Proxy[] findBy(array $attributes)
 * @method static Prescription[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Prescription[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<Prescription> create(array|callable $attributes = [])
 * @psalm-method static Proxy<Prescription> createOne(array $attributes = [])
 * @psalm-method static Proxy<Prescription> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<Prescription> findOrCreate(array $attributes)
 * @psalm-method static Proxy<Prescription> first(string $sortedField = 'id')
 * @psalm-method static Proxy<Prescription> last(string $sortedField = 'id')
 * @psalm-method static Proxy<Prescription> random(array $attributes = [])
 * @psalm-method static Proxy<Prescription> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<Prescription> repository()
 * @psalm-method static list<Proxy<Prescription>> all()
 * @psalm-method static list<Proxy<Prescription>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<Prescription>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<Prescription>> findBy(array $attributes)
 * @psalm-method static list<Proxy<Prescription>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<Prescription>> randomSet(int $number, array $attributes = [])
 */
final class PrescriptionFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'visit' => VisitFactory::randomOrCreate(),
            'confirmed' => false,
            'creationDate' => self::faker()->dateTime('-5 years'),
            'medications' => PrescriptionEntryFactory::randomRange(1,5),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Prescription $prescription): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Prescription::class;
    }
}
