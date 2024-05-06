<?php

namespace App\Factory;

use App\Entity\Pharmacy;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Pharmacy>
 *
 * @method        Pharmacy|Proxy create(array|callable $attributes = [])
 * @method static Pharmacy|Proxy createOne(array $attributes = [])
 * @method static Pharmacy|Proxy find(object|array|mixed $criteria)
 * @method static Pharmacy|Proxy findOrCreate(array $attributes)
 * @method static Pharmacy|Proxy first(string $sortedField = 'id')
 * @method static Pharmacy|Proxy last(string $sortedField = 'id')
 * @method static Pharmacy|Proxy random(array $attributes = [])
 * @method static Pharmacy|Proxy randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static Pharmacy[]|Proxy[] all()
 * @method static Pharmacy[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Pharmacy[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Pharmacy[]|Proxy[] findBy(array $attributes)
 * @method static Pharmacy[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Pharmacy[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<Pharmacy> create(array|callable $attributes = [])
 * @psalm-method static Proxy<Pharmacy> createOne(array $attributes = [])
 * @psalm-method static Proxy<Pharmacy> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<Pharmacy> findOrCreate(array $attributes)
 * @psalm-method static Proxy<Pharmacy> first(string $sortedField = 'id')
 * @psalm-method static Proxy<Pharmacy> last(string $sortedField = 'id')
 * @psalm-method static Proxy<Pharmacy> random(array $attributes = [])
 * @psalm-method static Proxy<Pharmacy> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<Pharmacy> repository()
 * @psalm-method static list<Proxy<Pharmacy>> all()
 * @psalm-method static list<Proxy<Pharmacy>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<Pharmacy>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<Pharmacy>> findBy(array $attributes)
 * @psalm-method static list<Proxy<Pharmacy>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<Pharmacy>> randomSet(int $number, array $attributes = [])
 */
final class PharmacyFactory extends ModelFactory
{
    public const DEFAULT_PASSWORD = 'pharmacy';

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
            'username' => self::faker()->userName(),
            'password' => '$argon2id$v=19$m=60000,t=10,p=1$TjSKKV96qv4HXeNnJ+U03Q$Pviu3e7sfB+CVwUm40BjzJBJO3gDrWa9FPxozf/CdDQ',
            'name' => self::faker()->company(),
            'address' => self::faker()->streetAddress(),
            'email' => self::faker()->boolean(80) ?
                self::faker()->email() :
                null,
            'isVerified' => false,
            'number' => self::faker()->boolean() ?
                self::faker()->randomNumber(8, true) :
                null,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function (Pharmacy $pharmacy): void {
            //     $pharmacy->setPassword($this->hasher->hashPassword($pharmacy, $pharmacy->getUsername()));
            // })
        ;
    }

    protected static function getClass(): string
    {
        return Pharmacy::class;
    }
}
