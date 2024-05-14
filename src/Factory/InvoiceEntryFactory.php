<?php

namespace App\Factory;

use App\Entity\InvoiceEntry;
use App\Repository\InvoiceEntryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<InvoiceEntry>
 *
 * @method        InvoiceEntry|Proxy create(array|callable $attributes = [])
 * @method static InvoiceEntry|Proxy createOne(array $attributes = [])
 * @method static InvoiceEntry|Proxy find(object|array|mixed $criteria)
 * @method static InvoiceEntry|Proxy findOrCreate(array $attributes)
 * @method static InvoiceEntry|Proxy first(string $sortedField = 'id')
 * @method static InvoiceEntry|Proxy last(string $sortedField = 'id')
 * @method static InvoiceEntry|Proxy random(array $attributes = [])
 * @method static InvoiceEntry|Proxy randomOrCreate(array $attributes = [])
 * @method static InvoiceEntryRepository|RepositoryProxy repository()
 * @method static InvoiceEntry[]|Proxy[] all()
 * @method static InvoiceEntry[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static InvoiceEntry[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static InvoiceEntry[]|Proxy[] findBy(array $attributes)
 * @method static InvoiceEntry[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static InvoiceEntry[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<InvoiceEntry> create(array|callable $attributes = [])
 * @psalm-method static Proxy<InvoiceEntry> createOne(array $attributes = [])
 * @psalm-method static Proxy<InvoiceEntry> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<InvoiceEntry> findOrCreate(array $attributes)
 * @psalm-method static Proxy<InvoiceEntry> first(string $sortedField = 'id')
 * @psalm-method static Proxy<InvoiceEntry> last(string $sortedField = 'id')
 * @psalm-method static Proxy<InvoiceEntry> random(array $attributes = [])
 * @psalm-method static Proxy<InvoiceEntry> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<InvoiceEntry> repository()
 * @psalm-method static list<Proxy<InvoiceEntry>> all()
 * @psalm-method static list<Proxy<InvoiceEntry>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<InvoiceEntry>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<InvoiceEntry>> findBy(array $attributes)
 * @psalm-method static list<Proxy<InvoiceEntry>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<InvoiceEntry>> randomSet(int $number, array $attributes = [])
 */
final class InvoiceEntryFactory extends ModelFactory
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
            'cost' => self::faker()->randomFloat(nbMaxDecimals: 2, min: 3, max: 22),
            'invoice' => null,
            'medication' => MedicationFactory::randomOrCreate(),
            'quantity' => self::faker()->numberBetween(1, 6),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(InvoiceEntry $invoiceEntry): void {})
        ;
    }

    protected static function getClass(): string
    {
        return InvoiceEntry::class;
    }
}
