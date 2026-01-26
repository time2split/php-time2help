<?php

declare(strict_types=1);

namespace Time2Split\Help\Memory\_internal;

use Time2Split\Help\Arrays;
use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\Set;
use Time2Split\Help\Container\Sets;
use Time2Split\Help\Exception\UnmodifiableException;
use Time2Split\Help\Memory\EnumSetMemoizer;
use Time2Split\Help\Optional;
use Traversable;
use UnitEnum;

/**
 * @author Olivier Rodriguez (zuri)
 * 
 * @template E of \UnitEnum
 */
class EnumSetMemoizerBitIndexImpl
implements
    EnumSetMemoizer,
    \IteratorAggregate
{
    protected function __construct(
        /**
         * @phpstan-var class-string
         */
        private string $enumClass,

        /**
         * @var null|E[][]
         */
        private null|array $allowedCases,

        /**
         * @throws \InvalidArgumentException If $enumClass is invalid.
         */
        private \SplObjectStorage $index,

        /**
         * @var Set<E>[]
         */
        protected array $cache,
    ) {}

    public static function create(string $enumClass, ?array $allowedCases): self
    {
        assert(\is_subclass_of($enumClass, \UnitEnum::class));
        return new self(
            $enumClass,
            $allowedCases,
            cache: [],
            index: self::createIndex($enumClass),
        );
    }

    #[\Override]
    public function getIterator(): Traversable
    {
        foreach ($this->cache as $set)
            yield $set->toListOfElements() => $set;
    }

    #[\Override]
    public function memoize(\UnitEnum ...$cases): Set&IsUnmodifiable
    {
        $this->checkAllowedCases($cases);
        $index = $this->getIndexOf($cases);
        return $this->cache[$index] ??= $this->createSet($cases)->unmodifiable();
    }

    #[\Override]
    public function getEnumClass(): string
    {
        return $this->enumClass;
    }

    #[\Override]
    public function count(): int
    {
        return \count($this->cache);
    }

    #[\Override]
    public function clear(): void
    {
        $this->cache = [];
    }

    #[\Override]
    public function copy(): static
    {
        return clone $this;
    }

    #[\Override]
    public function unmodifiable(): IsUnmodifiable
    {
        return new class(
            $this->enumClass,
            $this->allowedCases,
            $this->index,
            $this->cache,
        ) extends EnumSetMemoizerBitIndexImpl implements IsUnmodifiable {

            #[\Override]
            public function memoize(UnitEnum ...$cases): Set&IsUnmodifiable
            {
                $index = $this->getIndexIfExists(...$cases);

                if (!$index->isPresent())
                    throw new UnmodifiableException;

                return $this->cache[$index->get()];
            }
        };
    }

    //=========================================================================

    /**
     * Create an index where each enum case is associated with a unique integer with a single bit.
     */
    private static function createIndex(string $enumClass): \SplObjectStorage
    {
        $cases = $enumClass::cases();
        $nbCases = \count($cases);

        assert(
            $nbCases <= PHP_INT_BITS,
            "The number of $enumClass cases ($nbCases) is greater than the number of bits of an integer)"
        );

        // TODO: optimize avoiding the unused enum cases
        $index = new \SplObjectStorage();
        $i = 1;

        foreach ($cases as $case) {
            $index[$case] = $i;
            $i <<= 1;
        }
        return $index;
    }

    // protected abstract function getIndexOf(array $cases): int;

    private function getIndexOf(array $cases): int
    {
        $i = 0;

        foreach ($cases as $case)
            $i |= $this->index[$case];

        return $i;
    }

    /**
     * @param E[] $cases
     * @return Optional<int>
     */
    protected final function getIndexIfExists(array $cases): Optional
    {
        $index = $this->getIndexOf($cases);
        return Arrays::getValueIfKeyExists($this->cache, $index);
    }

    private function createSet(array $cases): Set
    {
        return Sets::ofEnum($this->enumClass)
            ->putFromList($cases);
    }

    private function checkAllowedCases(array $cases): void
    {
        if (
            isset($this->allowedCases)
            && !\in_array($cases, $this->allowedCases, true)
        ) {
            $types = \implode(',', \array_map(fn($t) => $t->name, $cases));
            throw new \InvalidArgumentException(
                "Unknown combinainon of $this->enumClass ($types)"
            );
        } else {

            foreach ($cases as $case) {

                if (!($case instanceof $this->enumClass))
                    throw new \InvalidArgumentException(
                        "$case->name is not of type $this->enumClass"
                    );
            }
        }
    }
}
