<?php

namespace App\Factory;

use App\Entity\Issue;
use App\Repository\IssueRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Issue>
 *
 * @method        Issue|Proxy create(array|callable $attributes = [])
 * @method static Issue|Proxy createOne(array $attributes = [])
 * @method static Issue|Proxy find(object|array|mixed $criteria)
 * @method static Issue|Proxy findOrCreate(array $attributes)
 * @method static Issue|Proxy first(string $sortedField = 'id')
 * @method static Issue|Proxy last(string $sortedField = 'id')
 * @method static Issue|Proxy random(array $attributes = [])
 * @method static Issue|Proxy randomOrCreate(array $attributes = [])
 * @method static IssueRepository|RepositoryProxy repository()
 * @method static Issue[]|Proxy[] all()
 * @method static Issue[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Issue[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Issue[]|Proxy[] findBy(array $attributes)
 * @method static Issue[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Issue[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<Issue> create(array|callable $attributes = [])
 * @psalm-method static Proxy<Issue> createOne(array $attributes = [])
 * @psalm-method static Proxy<Issue> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<Issue> findOrCreate(array $attributes)
 * @psalm-method static Proxy<Issue> first(string $sortedField = 'id')
 * @psalm-method static Proxy<Issue> last(string $sortedField = 'id')
 * @psalm-method static Proxy<Issue> random(array $attributes = [])
 * @psalm-method static Proxy<Issue> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<Issue> repository()
 * @psalm-method static list<Proxy<Issue>> all()
 * @psalm-method static list<Proxy<Issue>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<Issue>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<Issue>> findBy(array $attributes)
 * @psalm-method static list<Proxy<Issue>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<Issue>> randomSet(int $number, array $attributes = [])
 */
final class IssueFactory extends ModelFactory
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
            'category' => self::faker()->randomElement(['Pharmacy','Doctor','Medication','Other']),
            'content' => self::faker()->words(20, true),
            'creationDate' => self::faker()->dateTime( '-1 year'),
            'title' => self::faker()->words(3,true),
            'user' => PatientFactory::randomOrCreate(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Issue $issue): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Issue::class;
    }
}
