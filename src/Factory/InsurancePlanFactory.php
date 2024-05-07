<?php

namespace App\Factory;

use App\Entity\InsurancePlan;
use App\Repository\InsurancePlanRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<InsurancePlan>
 *
 * @method        InsurancePlan|Proxy create(array|callable $attributes = [])
 * @method static InsurancePlan|Proxy createOne(array $attributes = [])
 * @method static InsurancePlan|Proxy find(object|array|mixed $criteria)
 * @method static InsurancePlan|Proxy findOrCreate(array $attributes)
 * @method static InsurancePlan|Proxy first(string $sortedField = 'id')
 * @method static InsurancePlan|Proxy last(string $sortedField = 'id')
 * @method static InsurancePlan|Proxy random(array $attributes = [])
 * @method static InsurancePlan|Proxy randomOrCreate(array $attributes = [])
 * @method static InsurancePlanRepository|RepositoryProxy repository()
 * @method static InsurancePlan[]|Proxy[] all()
 * @method static InsurancePlan[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static InsurancePlan[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static InsurancePlan[]|Proxy[] findBy(array $attributes)
 * @method static InsurancePlan[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static InsurancePlan[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<InsurancePlan> create(array|callable $attributes = [])
 * @psalm-method static Proxy<InsurancePlan> createOne(array $attributes = [])
 * @psalm-method static Proxy<InsurancePlan> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<InsurancePlan> findOrCreate(array $attributes)
 * @psalm-method static Proxy<InsurancePlan> first(string $sortedField = 'id')
 * @psalm-method static Proxy<InsurancePlan> last(string $sortedField = 'id')
 * @psalm-method static Proxy<InsurancePlan> random(array $attributes = [])
 * @psalm-method static Proxy<InsurancePlan> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<InsurancePlan> repository()
 * @psalm-method static list<Proxy<InsurancePlan>> all()
 * @psalm-method static list<Proxy<InsurancePlan>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<InsurancePlan>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<InsurancePlan>> findBy(array $attributes)
 * @psalm-method static list<Proxy<InsurancePlan>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<InsurancePlan>> randomSet(int $number, array $attributes = [])
 */
final class InsurancePlanFactory extends ModelFactory
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
        $cost = self::faker()->randomFloat(nbMaxDecimals: 2, min: 500, max: 2000);
        return [
            'ceiling' => self::faker()->randomFloat(nbMaxDecimals: 2, min: 100, max: $cost - 100),
            'cost' => $cost,
            'description' => self::faker()->realTextBetween(30, 35),
            'name' => self::faker()->domainWord(),
            'reimbursementRate' => self::faker()->randomFloat(nbMaxDecimals: 2, min: 0.1, max: 0.8),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(InsurancePlan $insurancePlan): void {})
        ;
    }

    protected static function getClass(): string
    {
        return InsurancePlan::class;
    }
}
