<?php

namespace App\Factory;

use App\Entity\Patient;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Patient>
 *
 * @method        Patient|Proxy create(array|callable $attributes = [])
 * @method static Patient|Proxy createOne(array $attributes = [])
 * @method static Patient|Proxy find(object|array|mixed $criteria)
 * @method static Patient|Proxy findOrCreate(array $attributes)
 * @method static Patient|Proxy first(string $sortedField = 'id')
 * @method static Patient|Proxy last(string $sortedField = 'id')
 * @method static Patient|Proxy random(array $attributes = [])
 * @method static Patient|Proxy randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static Patient[]|Proxy[] all()
 * @method static Patient[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Patient[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Patient[]|Proxy[] findBy(array $attributes)
 * @method static Patient[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Patient[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<Patient> create(array|callable $attributes = [])
 * @psalm-method static Proxy<Patient> createOne(array $attributes = [])
 * @psalm-method static Proxy<Patient> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<Patient> findOrCreate(array $attributes)
 * @psalm-method static Proxy<Patient> first(string $sortedField = 'id')
 * @psalm-method static Proxy<Patient> last(string $sortedField = 'id')
 * @psalm-method static Proxy<Patient> random(array $attributes = [])
 * @psalm-method static Proxy<Patient> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<Patient> repository()
 * @psalm-method static list<Proxy<Patient>> all()
 * @psalm-method static list<Proxy<Patient>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<Patient>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<Patient>> findBy(array $attributes)
 * @psalm-method static list<Proxy<Patient>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<Patient>> randomSet(int $number, array $attributes = [])
 */
final class PatientFactory extends ModelFactory
{
    public const DEFAULT_PASSWORD = 'patient';

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
            'password' => '$argon2id$v=19$m=60000,t=10,p=1$qbc4hRMMFwXwHAPYC67K8w$IqUnTi0THYHsgHtF5d4wHCEB9qt3UDts5F7adWiC3rc',
            'email' => self::faker()->boolean(30) ?
                self::faker()->email() :
                null,
            'isVerified' => false,
            'activationCode' => null,
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'address' => self::faker()->streetAddress(),
            'dateOfBirth' => self::faker()->dateTime('-12 years'),
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
            // ->afterInstantiate(function (Patient $patient): void {
            //     $patient->setPassword($this->hasher->hashPassword($patient, $patient->getUsername()));
            // })
        ;
    }

    protected static function getClass(): string
    {
        return Patient::class;
    }
}
