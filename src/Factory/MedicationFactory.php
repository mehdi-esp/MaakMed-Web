<?php

namespace App\Factory;

use App\Entity\Medication;
use App\Repository\MedicationRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Medication>
 *
 * @method        Medication|Proxy create(array|callable $attributes = [])
 * @method static Medication|Proxy createOne(array $attributes = [])
 * @method static Medication|Proxy find(object|array|mixed $criteria)
 * @method static Medication|Proxy findOrCreate(array $attributes)
 * @method static Medication|Proxy first(string $sortedField = 'id')
 * @method static Medication|Proxy last(string $sortedField = 'id')
 * @method static Medication|Proxy random(array $attributes = [])
 * @method static Medication|Proxy randomOrCreate(array $attributes = [])
 * @method static MedicationRepository|RepositoryProxy repository()
 * @method static Medication[]|Proxy[] all()
 * @method static Medication[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Medication[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Medication[]|Proxy[] findBy(array $attributes)
 * @method static Medication[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Medication[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<Medication> create(array|callable $attributes = [])
 * @psalm-method static Proxy<Medication> createOne(array $attributes = [])
 * @psalm-method static Proxy<Medication> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<Medication> findOrCreate(array $attributes)
 * @psalm-method static Proxy<Medication> first(string $sortedField = 'id')
 * @psalm-method static Proxy<Medication> last(string $sortedField = 'id')
 * @psalm-method static Proxy<Medication> random(array $attributes = [])
 * @psalm-method static Proxy<Medication> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<Medication> repository()
 * @psalm-method static list<Proxy<Medication>> all()
 * @psalm-method static list<Proxy<Medication>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<Medication>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<Medication>> findBy(array $attributes)
 * @psalm-method static list<Proxy<Medication>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<Medication>> randomSet(int $number, array $attributes = [])
 */
final class MedicationFactory extends ModelFactory
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
            'description' => self::faker()->realText(),
            'insured' => self::faker()->boolean(),
            'name' => self::faker()->word(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Medication $medication): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Medication::class;
    }
}
