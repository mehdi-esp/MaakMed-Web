<?php

namespace App\Factory;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Subscription>
 *
 * @method        Subscription|Proxy                     create(array|callable $attributes = [])
 * @method static Subscription|Proxy                     createOne(array $attributes = [])
 * @method static Subscription|Proxy                     find(object|array|mixed $criteria)
 * @method static Subscription|Proxy                     findOrCreate(array $attributes)
 * @method static Subscription|Proxy                     first(string $sortedField = 'id')
 * @method static Subscription|Proxy                     last(string $sortedField = 'id')
 * @method static Subscription|Proxy                     random(array $attributes = [])
 * @method static Subscription|Proxy                     randomOrCreate(array $attributes = [])
 * @method static SubscriptionRepository|RepositoryProxy repository()
 * @method static Subscription[]|Proxy[]                 all()
 * @method static Subscription[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Subscription[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Subscription[]|Proxy[]                 findBy(array $attributes)
 * @method static Subscription[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Subscription[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<Subscription> create(array|callable $attributes = [])
 * @psalm-method static Proxy<Subscription> createOne(array $attributes = [])
 * @psalm-method static Proxy<Subscription> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<Subscription> findOrCreate(array $attributes)
 * @psalm-method static Proxy<Subscription> first(string $sortedField = 'id')
 * @psalm-method static Proxy<Subscription> last(string $sortedField = 'id')
 * @psalm-method static Proxy<Subscription> random(array $attributes = [])
 * @psalm-method static Proxy<Subscription> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<Subscription> repository()
 * @psalm-method static list<Proxy<Subscription>> all()
 * @psalm-method static list<Proxy<Subscription>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<Subscription>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<Subscription>> findBy(array $attributes)
 * @psalm-method static list<Proxy<Subscription>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<Subscription>> randomSet(int $number, array $attributes = [])
 */
final class SubscriptionFactory extends ModelFactory
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
        //from the start of the current year
        $startDate = self::faker()->dateTimeBetween('-1 year', 'now');
        return [
            //start date + 1 year
            'startDate' => clone $startDate,
            'endDate' => $startDate->modify('+1 year'),
            'patient' => PatientFactory::randomOrCreate(),
            'plan' => InsurancePlanFactory::randomOrCreate(),
            'status' => 'canceled'
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Subscription $subscription): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Subscription::class;
    }
}
