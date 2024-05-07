<?php

namespace App\Factory;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Invoice>
 *
 * @method        Invoice|Proxy create(array|callable $attributes = [])
 * @method static Invoice|Proxy createOne(array $attributes = [])
 * @method static Invoice|Proxy find(object|array|mixed $criteria)
 * @method static Invoice|Proxy findOrCreate(array $attributes)
 * @method static Invoice|Proxy first(string $sortedField = 'id')
 * @method static Invoice|Proxy last(string $sortedField = 'id')
 * @method static Invoice|Proxy random(array $attributes = [])
 * @method static Invoice|Proxy randomOrCreate(array $attributes = [])
 * @method static InvoiceRepository|RepositoryProxy repository()
 * @method static Invoice[]|Proxy[] all()
 * @method static Invoice[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Invoice[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Invoice[]|Proxy[] findBy(array $attributes)
 * @method static Invoice[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Invoice[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<Invoice> create(array|callable $attributes = [])
 * @psalm-method static Proxy<Invoice> createOne(array $attributes = [])
 * @psalm-method static Proxy<Invoice> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<Invoice> findOrCreate(array $attributes)
 * @psalm-method static Proxy<Invoice> first(string $sortedField = 'id')
 * @psalm-method static Proxy<Invoice> last(string $sortedField = 'id')
 * @psalm-method static Proxy<Invoice> random(array $attributes = [])
 * @psalm-method static Proxy<Invoice> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<Invoice> repository()
 * @psalm-method static list<Proxy<Invoice>> all()
 * @psalm-method static list<Proxy<Invoice>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<Invoice>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<Invoice>> findBy(array $attributes)
 * @psalm-method static list<Proxy<Invoice>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<Invoice>> randomSet(int $number, array $attributes = [])
 */
final class InvoiceFactory extends ModelFactory
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
            'creationTime' => self::faker()->dateTimeBetween('-5 years'),
            'discountRate' => 0,
            'patient' => PatientFactory::randomOrCreate(),
            'pharmacy' => PharmacyFactory::randomOrCreate(),
            'invoiceEntries' => InvoiceEntryFactory::randomRange(0, 0),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Invoice $invoice): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Invoice::class;
    }
}
