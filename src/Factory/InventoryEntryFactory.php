<?php

namespace App\Factory;

use App\Entity\InventoryEntry;
use App\Repository\InventoryEntryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<InventoryEntry>
 *
 * @method        InventoryEntry|Proxy create(array|callable $attributes = [])
 * @method static InventoryEntry|Proxy createOne(array $attributes = [])
 * @method static InventoryEntry|Proxy find(object|array|mixed $criteria)
 * @method static InventoryEntry|Proxy findOrCreate(array $attributes)
 * @method static InventoryEntry|Proxy first(string $sortedField = 'id')
 * @method static InventoryEntry|Proxy last(string $sortedField = 'id')
 * @method static InventoryEntry|Proxy random(array $attributes = [])
 * @method static InventoryEntry|Proxy randomOrCreate(array $attributes = [])
 * @method static InventoryEntryRepository|RepositoryProxy repository()
 * @method static InventoryEntry[]|Proxy[] all()
 * @method static InventoryEntry[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static InventoryEntry[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static InventoryEntry[]|Proxy[] findBy(array $attributes)
 * @method static InventoryEntry[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static InventoryEntry[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<InventoryEntry> create(array|callable $attributes = [])
 * @psalm-method static Proxy<InventoryEntry> createOne(array $attributes = [])
 * @psalm-method static Proxy<InventoryEntry> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<InventoryEntry> findOrCreate(array $attributes)
 * @psalm-method static Proxy<InventoryEntry> first(string $sortedField = 'id')
 * @psalm-method static Proxy<InventoryEntry> last(string $sortedField = 'id')
 * @psalm-method static Proxy<InventoryEntry> random(array $attributes = [])
 * @psalm-method static Proxy<InventoryEntry> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<InventoryEntry> repository()
 * @psalm-method static list<Proxy<InventoryEntry>> all()
 * @psalm-method static list<Proxy<InventoryEntry>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<InventoryEntry>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<InventoryEntry>> findBy(array $attributes)
 * @psalm-method static list<Proxy<InventoryEntry>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<InventoryEntry>> randomSet(int $number, array $attributes = [])
 */
final class InventoryEntryFactory extends ModelFactory
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
            'medication' => MedicationFactory::randomOrCreate(),
            'pharmacy' => PharmacyFactory::randomOrCreate(),
            'quantity' => self::faker()->numberBetween( 45, 300 ),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(InventoryEntry $inventoryEntry): void {})
        ;
    }

    protected static function getClass(): string
    {
        return InventoryEntry::class;
    }
}
