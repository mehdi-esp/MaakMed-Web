<?php

namespace App\Factory;

use App\Entity\Visit;
use App\Entity\VisitCategory;
use App\Repository\UserRepository;
use App\Repository\VisitRepository;
use DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Visit>
 *
 * @method        Visit|Proxy create(array|callable $attributes = [])
 * @method static Visit|Proxy createOne(array $attributes = [])
 * @method static Visit|Proxy find(object|array|mixed $criteria)
 * @method static Visit|Proxy findOrCreate(array $attributes)
 * @method static Visit|Proxy first(string $sortedField = 'id')
 * @method static Visit|Proxy last(string $sortedField = 'id')
 * @method static Visit|Proxy random(array $attributes = [])
 * @method static Visit|Proxy randomOrCreate(array $attributes = [])
 * @method static VisitRepository|RepositoryProxy repository()
 * @method static Visit[]|Proxy[] all()
 * @method static Visit[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Visit[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Visit[]|Proxy[] findBy(array $attributes)
 * @method static Visit[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Visit[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<Visit> create(array|callable $attributes = [])
 * @psalm-method static Proxy<Visit> createOne(array $attributes = [])
 * @psalm-method static Proxy<Visit> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<Visit> findOrCreate(array $attributes)
 * @psalm-method static Proxy<Visit> first(string $sortedField = 'id')
 * @psalm-method static Proxy<Visit> last(string $sortedField = 'id')
 * @psalm-method static Proxy<Visit> random(array $attributes = [])
 * @psalm-method static Proxy<Visit> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<Visit> repository()
 * @psalm-method static list<Proxy<Visit>> all()
 * @psalm-method static list<Proxy<Visit>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<Visit>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<Visit>> findBy(array $attributes)
 * @psalm-method static list<Proxy<Visit>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<Visit>> randomSet(int $number, array $attributes = [])
 */
final class VisitFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(private readonly UserRepository $userRepository)
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
            'date' => self::faker()->dateTimeBetween('-5 years'),
            'diagnosis' => self::faker()->realTextBetween(20, 100),
            'doctor' => DoctorFactory::randomOrCreate(),
            'patient' => PatientFactory::randomOrCreate(),
            'type' => self::faker()->randomElement(VisitCategory::cases()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Visit $visit): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Visit::class;
    }
}
