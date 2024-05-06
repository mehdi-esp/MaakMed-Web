<?php

namespace App\Factory;

use App\Entity\Admin;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Admin>
 *
 * @method        Admin|Proxy create(array|callable $attributes = [])
 * @method static Admin|Proxy createOne(array $attributes = [])
 * @method static Admin|Proxy find(object|array|mixed $criteria)
 * @method static Admin|Proxy findOrCreate(array $attributes)
 * @method static Admin|Proxy first(string $sortedField = 'id')
 * @method static Admin|Proxy last(string $sortedField = 'id')
 * @method static Admin|Proxy random(array $attributes = [])
 * @method static Admin|Proxy randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static Admin[]|Proxy[] all()
 * @method static Admin[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Admin[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Admin[]|Proxy[] findBy(array $attributes)
 * @method static Admin[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Admin[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<Admin> create(array|callable $attributes = [])
 * @psalm-method static Proxy<Admin> createOne(array $attributes = [])
 * @psalm-method static Proxy<Admin> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<Admin> findOrCreate(array $attributes)
 * @psalm-method static Proxy<Admin> first(string $sortedField = 'id')
 * @psalm-method static Proxy<Admin> last(string $sortedField = 'id')
 * @psalm-method static Proxy<Admin> random(array $attributes = [])
 * @psalm-method static Proxy<Admin> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<Admin> repository()
 * @psalm-method static list<Proxy<Admin>> all()
 * @psalm-method static list<Proxy<Admin>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<Admin>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<Admin>> findBy(array $attributes)
 * @psalm-method static list<Proxy<Admin>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<Admin>> randomSet(int $number, array $attributes = [])
 */
final class AdminFactory extends ModelFactory
{
    public const DEFAULT_PASSWORD = 'admin';

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {
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
            'email' => self::faker()->boolean(20) ?
                self::faker()->email() :
                null,
            'password' => '$argon2id$v=19$m=60000,t=10,p=1$QzTKHfbykCeiBGHWysm69A$1WJomjBrzNMEPlsCC3qNoWYlzNxDlcf5H/hvZ24fBT4',
            'username' => self::faker()->userName()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Admin $admin): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Admin::class;
    }
}
