<?php

namespace App\Factory;

use App\Entity\Doctor;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Doctor>
 *
 * @method        Doctor|Proxy create(array|callable $attributes = [])
 * @method static Doctor|Proxy createOne(array $attributes = [])
 * @method static Doctor|Proxy find(object|array|mixed $criteria)
 * @method static Doctor|Proxy findOrCreate(array $attributes)
 * @method static Doctor|Proxy first(string $sortedField = 'id')
 * @method static Doctor|Proxy last(string $sortedField = 'id')
 * @method static Doctor|Proxy random(array $attributes = [])
 * @method static Doctor|Proxy randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static Doctor[]|Proxy[] all()
 * @method static Doctor[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Doctor[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Doctor[]|Proxy[] findBy(array $attributes)
 * @method static Doctor[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Doctor[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<Doctor> create(array|callable $attributes = [])
 * @psalm-method static Proxy<Doctor> createOne(array $attributes = [])
 * @psalm-method static Proxy<Doctor> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<Doctor> findOrCreate(array $attributes)
 * @psalm-method static Proxy<Doctor> first(string $sortedField = 'id')
 * @psalm-method static Proxy<Doctor> last(string $sortedField = 'id')
 * @psalm-method static Proxy<Doctor> random(array $attributes = [])
 * @psalm-method static Proxy<Doctor> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<Doctor> repository()
 * @psalm-method static list<Proxy<Doctor>> all()
 * @psalm-method static list<Proxy<Doctor>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<Doctor>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<Doctor>> findBy(array $attributes)
 * @psalm-method static list<Proxy<Doctor>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<Doctor>> randomSet(int $number, array $attributes = [])
 */
final class DoctorFactory extends ModelFactory
{
    public const DEFAULT_PASSWORD = 'doctor';

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
            'username' => self::faker()->unique()->userName(),
            'password' => '$argon2id$v=19$m=60000,t=10,p=1$fkVyGE1vA0D1beaAwIImQA$MGy6K20us4GHBG3dlHlHOvEqxVfsAbZp9j1+5vchaWw',
            'email' => self::faker()->boolean(60) ?
                self::faker()->email() :
                null,
            'isVerified' => false,
            'activationCode' => null,
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'specialization' => self::faker()->words(2, true),
            'nationalId' => self::faker()->randomNumber(8),
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
            // ->afterInstantiate(function (Doctor $doctor): void {
            //     $doctor->setPassword($this->hasher->hashPassword($doctor, $doctor->getUsername()));
            // })
        ;
    }

    protected static function getClass(): string
    {
        return Doctor::class;
    }
}
