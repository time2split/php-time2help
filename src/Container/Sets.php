<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Cast\Cast;
use Time2Split\Help\Classes\IsUnmodifiable;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\_internal\SetWithStorage;
use Time2Split\Help\Container\Trait\IteratorToArray;
use Time2Split\Help\Container\Trait\IteratorToArrayContainer;
use Time2Split\Help\Container\Trait\UnmodifiableArrayAccessContainer;
use Time2Split\Help\Container\Trait\UnmodifiableContainerPutMethods;
use Time2Split\Help\Iterables;

/**
 * Factories and functions on set.
 * 
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 */
final class Sets
{
    use NotInstanciable;

    private static function create(ContainerAA $storage): Set
    {
        return new class($storage)
        extends SetWithStorage {
            #[\Override]
            public function getIterator(): \Traversable
            {
                return Cast::iterableToIterator(
                    Cast::iterableToIterator(Iterables::keys(parent::getIterator()))
                );
            }
        };
    }

    /**
     * Provides a set storing items as array keys.
     *
     * This set is only convenient for data types that can fit as array keys.
     *
     * @return Set<string|int> A new set.
     */
    public static function arrayKeys(): Set
    {
        return self::create(ArrayContainers::create());
    }

    /**
     * Provides a set storing arbitrary items as array keys.
     * 
     * Internally it gets a {@see Sets::arrayKeys()} to store the items.
     * This Set can be used when an element can be associated with a unique array key identifier.
     *
     * This class permits to handle more types of values and not just array keys.
     * It makes a bijection between a valid array key and an element.
     *
     * @param callable $mapKey
     *            Map an input item to a valid key.
     * @return Set<mixed> A new Set.
     */
    public static function toArrayKeys(callable $mapKey): Set
    {
        return self::create(ArrayContainers::toArrayKeys($mapKey));
    }

    /**
     * A set able to store \UnitEnum instances.
     * 
     * Internally it uses a `\SplObjectStorage` as storage of the enum values.
     *
     * @template T of \UnitEnum
     * @param string|T $enumClass
     *            The enum class of the elements to store.
     *            It may be a string class name of T or a T instance.
     * @return Set<T> A new Set.
     * 
     * @link https://www.php.net/manual/en/class.unitenum.php \UnitEnum
     */
    public static function ofEnum(string|object $enumClass = \UnitEnum::class): Set
    {
        if (!\is_a($enumClass, \UnitEnum::class, true))
            throw new \InvalidArgumentException("$enumClass must be a \UnitEnum");

        if (!\is_string($enumClass))
            $enumClass = \get_class($enumClass);

        return new class(
            ObjectContainers::create(),
            $enumClass
        ) extends SetWithStorage {
            use
                IteratorToArray,
                IteratorToArrayContainer;
            public function __construct(
                ContainerAA $storage,
                private readonly string $enumClass
            ) {
                parent::__construct($storage);
            }
            #[\Override]
            public function offsetSet(mixed $offset, mixed $value): void
            {
                if (!\is_a($offset, $this->enumClass, true)) {
                    $have = \get_class($offset);
                    throw new \InvalidArgumentException("The item to assign must be a $this->enumClass (have $have)");
                }
                parent::offsetSet($offset, $value);
            }
            #[\Override]
            public function copy(): static
            {
                return new self(
                    $this->storage->copy(),
                    $this->enumClass
                );
            }
            #[\Override]
            public function getIterator(): \Traversable
            {
                return Cast::iterableToIterator(Iterables::keys(parent::getIterator()));
            }
        };
    }

    /**
     * A set able to store \BackedEnum instances.
     * 
     * @template T of \BackedEnum
     * @param string|T $enumClass
     *            The backed enum class of the elements to store.
     *            It may be a string class name of T or a T instance.
     * @return Set<T> A new Set.
     * @link https://www.php.net/manual/en/class.backedenum.php \BackedEnum
     */
    public static function ofBackedEnum(string|object $enumClass = \BackedEnum::class): Set
    {
        if (!\is_a($enumClass, \BackedEnum::class, true))
            throw new \InvalidArgumentException("$enumClass must be a \BackedEnum");

        /** @var Set<T> */
        return self::ofEnum($enumClass);
    }

    /**
     * Decorates a set to be unmodifiable.
     *
     * Call to a mutable method of the set will throws a {@see Exception\UnmodifiableException}.
     *
     * @template T
     * 
     * @param Set<T> $set
     *            A set to decorate.
     * @return SetWithStorage<T> The backed unmodifiable set.
     */
    public static function unmodifiable(Set $set): Set
    {
        assert($set instanceof SetWithStorage);
        return new class($set->getStorage())
        extends SetWithStorage
        implements IsUnmodifiable
        {
            use UnmodifiableArrayAccessContainer,
                UnmodifiableContainerPutMethods;
        };
    }

    /**
     * Gets the null pattern unmodifiable set.
     *
     * The value is a singleton and may be compared with the `===` operator.
     * 
     * @return SetWithStorage<void> The unique null pattern set.
     */
    public static function null(): SetWithStorage
    {
        static $null = self::unmodifiable(self::arrayKeys());
        return $null;
    }

    // ========================================================================
    // OPERATIONS

    /**
     * Checks if two sets contains the same items.
     * 
     * @param Set<mixed> $a First set.
     * @param Set<mixed> $b Second set.
     * @return bool true if the two sets contains the same items, false otherwise.
     */
    public static function equals(Set $a, Set $b): bool
    {
        if ($a === $b)
            return true;
        if (\count($a) !== \count($b))
            return false;
        foreach ($a as $item) {
            if (!$b[$item])
                return false;
        }
        return true;
    }

    /**
     * Checks whether the items of a set are part of another set.
     * 
     * @param Set<mixed> $searchFor The items to search for.
     * @param Set<mixed> $inside The set to search in.
     * 
     * @return bool true if all the items of $searchFor are inside the set `$inside`.
     */
    public static function isIncludedIn(Set $searchFor, Set $inside): bool
    {
        if ($searchFor === $inside)
            return true;
        if (\count($searchFor) > \count($inside))
            return false;
        foreach ($searchFor as $item) {
            if (!$inside[$item])
                return false;
        }
        return true;
    }
}
