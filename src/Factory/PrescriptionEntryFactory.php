<?php

namespace App\Factory;

use App\Entity\PrescriptionEntry;
use App\Repository\PrescriptionEntryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<PrescriptionEntry>
 *
 * @method        PrescriptionEntry|Proxy create(array|callable $attributes = [])
 * @method static PrescriptionEntry|Proxy createOne(array $attributes = [])
 * @method static PrescriptionEntry|Proxy find(object|array|mixed $criteria)
 * @method static PrescriptionEntry|Proxy findOrCreate(array $attributes)
 * @method static PrescriptionEntry|Proxy first(string $sortedField = 'id')
 * @method static PrescriptionEntry|Proxy last(string $sortedField = 'id')
 * @method static PrescriptionEntry|Proxy random(array $attributes = [])
 * @method static PrescriptionEntry|Proxy randomOrCreate(array $attributes = [])
 * @method static PrescriptionEntryRepository|RepositoryProxy repository()
 * @method static PrescriptionEntry[]|Proxy[] all()
 * @method static PrescriptionEntry[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static PrescriptionEntry[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static PrescriptionEntry[]|Proxy[] findBy(array $attributes)
 * @method static PrescriptionEntry[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PrescriptionEntry[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @psalm-method        Proxy<PrescriptionEntry> create(array|callable $attributes = [])
 * @psalm-method static Proxy<PrescriptionEntry> createOne(array $attributes = [])
 * @psalm-method static Proxy<PrescriptionEntry> find(object|array|mixed $criteria)
 * @psalm-method static Proxy<PrescriptionEntry> findOrCreate(array $attributes)
 * @psalm-method static Proxy<PrescriptionEntry> first(string $sortedField = 'id')
 * @psalm-method static Proxy<PrescriptionEntry> last(string $sortedField = 'id')
 * @psalm-method static Proxy<PrescriptionEntry> random(array $attributes = [])
 * @psalm-method static Proxy<PrescriptionEntry> randomOrCreate(array $attributes = [])
 * @psalm-method static RepositoryProxy<PrescriptionEntry> repository()
 * @psalm-method static list<Proxy<PrescriptionEntry>> all()
 * @psalm-method static list<Proxy<PrescriptionEntry>> createMany(int $number, array|callable $attributes = [])
 * @psalm-method static list<Proxy<PrescriptionEntry>> createSequence(iterable|callable $sequence)
 * @psalm-method static list<Proxy<PrescriptionEntry>> findBy(array $attributes)
 * @psalm-method static list<Proxy<PrescriptionEntry>> randomRange(int $min, int $max, array $attributes = [])
 * @psalm-method static list<Proxy<PrescriptionEntry>> randomSet(int $number, array $attributes = [])
 */
final class PrescriptionEntryFactory extends ModelFactory
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
            'instructions' => self::faker()->text(),
            'medication' => MedicationFactory::randomOrCreate(),
            // 'prescription' => PrescriptionFactory::new(),
            'quantity' => self::faker()->randomNumber(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(PrescriptionEntry $prescriptionEntry): void {})
        ;
    }

    protected static function getClass(): string
    {
        return PrescriptionEntry::class;
    }
}
