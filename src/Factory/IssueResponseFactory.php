<?php

namespace App\Factory;

use App\Entity\IssueResponse;
use App\Repository\IssueResponseRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<IssueResponse>
 *
 * @method        IssueResponse|Proxy create(array|callable $attributes = [])
 * @method static IssueResponse|Proxy createOne(array $attributes = [])
 * @method static IssueResponse|Proxy find(object|array|mixed $criteria)
 * @method static IssueResponse|Proxy findOrCreate(array $attributes)
 * @method static IssueResponse|Proxy first(string $sortedField = 'id')
 * @method static IssueResponse|Proxy last(string $sortedField = 'id')
 * @method static IssueResponse|Proxy random(array $attributes = [])
 * @method static IssueResponse|Proxy randomOrCreate(array $attributes = [])
 * @method static IssueResponseRepository|RepositoryProxy repository()
 * @method static IssueResponse[]|Proxy[] all()
 * @method static IssueResponse[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static IssueResponse[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static IssueResponse[]|Proxy[] findBy(array $attributes)
 * @method static IssueResponse[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static IssueResponse[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<IssueResponse> create(array|callable $attributes = [])
 * @psalm-method static Proxy<IssueResponse> createOne(array $attributes = [])
 * @psalm-method static Proxy<IssueResponse> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<IssueResponse> findOrCreate(array $attributes)
 * @psalm-method static Proxy<IssueResponse> first(string $sortedField = 'id')
 * @psalm-method static Proxy<IssueResponse> last(string $sortedField = 'id')
 * @psalm-method static Proxy<IssueResponse> random(array $attributes = [])
 * @psalm-method static Proxy<IssueResponse> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<IssueResponse> repository()
 * @psalm-method static list<Proxy<IssueResponse>> all()
 * @psalm-method static list<Proxy<IssueResponse>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<IssueResponse>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<IssueResponse>> findBy(array $attributes)
 * @psalm-method static list<Proxy<IssueResponse>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<IssueResponse>> randomSet(int $number, array $attributes = [])
 */
final class IssueResponseFactory extends ModelFactory
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
            'content' => self::faker()->words(30,true),
            'creationDate' => self::faker()->dateTime(),
            'issue' => IssueFactory::new(),
            'respondent' => AdminFactory::randomOrCreate(), // TODO add App\Entity\User type manually
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(IssueResponse $issueResponse): void {})
        ;
    }

    protected static function getClass(): string
    {
        return IssueResponse::class;
    }
}
