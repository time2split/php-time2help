<?php

declare(strict_types=1);

namespace Time2Split\Help\Memory\_internal;

use SplObjectStorage;
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
 * @template E of UnitEnum
 * 
 * @implements EnumSetMemoizer<E>
 * @implements \IteratorAggregate<list<E>,Set<E>>
 */
class EnumSetMemoizerBitIndexImpl
implements
    EnumSetMemoizer,
    \IteratorAggregate
{
    /**
     * @throws \InvalidArgumentException If $enumClass is invalid.
     */
    protected function __construct(
        /**
         * @phpstan-var class-string<E>
         */
        private string $enumClass,

        /**
         * @phpstan-var null|list<E>
         */
        private null|array $allowedCases,

        /**
         * @phpstan-var SplObjectStorage<E,int>
         */
        private SplObjectStorage $index,

        /**
         * @phpstan-var array<int,Set<E>&IsUnmodifiable>
         */
        protected array $cache,
    ) {}

    /**
     * @return self
     * 
     * @phpstan-param class-string<E> $enumClass
     * @phpstan-param null|list<E> $allowedCases
     * @phpstan-return self<E>
     */
    public static function create(string $enumClass, ?array $allowedCases): self
    {
        if (!\is_a($enumClass, \UnitEnum::class, true))
            throw new \InvalidArgumentException("$enumClass must be a \UnitEnum");

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
    public function memoize(UnitEnum ...$cases): Set&IsUnmodifiable
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

    /**
     * @phpstan-return IsUnmodifiable&EnumSetMemoizer<E>
     */
    #[\Override]
    public function unmodifiable(): isUnmodifiable&EnumSetMemoizer
    {
        /** @phpstan-ignore return.type */
        return new class(
            $this->enumClass,
            $this->allowedCases,
            $this->index,
            $this->cache,
        ) extends EnumSetMemoizerBitIndexImpl implements IsUnmodifiable {

            #[\Override]
            public function memoize(UnitEnum ...$cases): Set&IsUnmodifiable
            {
                $set = $this->getCacheIfExists($cases);

                if (!$set->isPresent())
                    throw new UnmodifiableException;

                return $set->get();
            }
        };
    }

    //=========================================================================

    /**
     * Create an index where each enum case is associated with a unique integer with a single bit.
     * 
     * @phpstan-param class-string<E> $enumClass
     * @phpstan-return SplObjectStorage<E,int>
     */
    private static function createIndex(string $enumClass): SplObjectStorage
    {
        $cases = $enumClass::cases();
        $nbCases = \count($cases);

        assert(
            $nbCases <= PHP_INT_BITS,
            "The number of $enumClass cases ($nbCases) is greater than the number of bits of an integer)"
        );
        // TODO: optimize avoiding the unused enum cases

        /** @phpstan-var SplObjectStorage<E,int> */
        $index = new SplObjectStorage();
        $i = 1;

        foreach ($cases as $case) {
            $index[$case] = $i;
            $i <<= 1;
        }
        return $index;
    }

    // protected abstract function getIndexOf(array $cases): int;

    /**
     * @phpstan-param list<E> $cases
     */
    private function getIndexOf(array $cases): int
    {
        $i = 0;

        foreach ($cases as $case)
            $i |= $this->index[$case];

        return $i;
    }

    /**
     * @phpstan-param E[] $cases
     * @phpstan-return Optional<Set<E>&IsUnmodifiable>
     */
    protected final function getCacheIfExists(array $cases): Optional
    {
        $index = $this->getIndexOf($cases);
        return Arrays::value($this->cache, $index);
    }

    /**
     * @phpstan-param list<E> $cases
     * @phpstan-return Set<E>
     */
    private function createSet(array $cases): Set
    {
        return Sets::ofEnum($this->enumClass)
            ->putFromList($cases);
    }

    /**
     * @phpstan-param list<E> $cases
     */
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
