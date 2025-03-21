<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Closure;
use Time2Split\Help\Classes\IsUnmodifiable;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\_internal\BagWithStorage;
use Time2Split\Help\Container\Container;
use Time2Split\Help\Container\Trait\UnmodifiableArrayAccessContainer;
use Time2Split\Help\Container\Trait\UnmodifiableContainerPutMethods;
use Time2Split\Help\Iterables;

final class Bags
{
    use NotInstanciable;

    private static function create(Container $storage): Bag
    {
        return new class($storage)
        extends BagWithStorage {
            #[\Override]
            public function getIterator(): \Traversable
            {
                return Iterables::keys(parent::getIterator());
            }
        };
    }

    /**
     * Provides a bag storing items as array keys.
     *
     * This bag is only convenient for data types that can fit as array keys.
     *
     * @return Bag<string|int> A new bag.
     */
    public static function arrayKeys(): Bag
    {
        return self::create(ArrayContainers::create());
    }

    /**
     * Provides a bag storing arbitrary items as array keys.
     * 
     * Internally it gets a {@see Bags::arrayKeys()} to store the items.
     * This Bag can be used when an element can be associated with a unique array key identifier.
     *
     * This class permits to handle more types of values and not just array keys.
     * It makes a bijection between a valid array key and an element.
     * 
     * @template K
     * @template KMAP
     * 
     * @param Closure(K):KMAP $mapKey
     *            Map an input item to a valid key.
     * @return Bag<KMAP> A new Bag.
     */
    public static function toArrayKeys(Closure $mapKey): Bag
    {
        return self::create(ArrayContainers::toArrayKeys($mapKey));
    }

    /**
     * A bag able to store \UnitEnum instances.
     * 
     * Internally it uses a `\SplObjectStorage` as storage of the enum values.
     *
     * @template T of \UnitEnum
     * @param string|T $enumClass
     *            The enum class of the elements to store.
     *            It may be a string class name of T or a T instance.
     * @return Bag<T> A new Bag.
     * 
     * @link https://www.php.net/manual/en/class.unitenum.php \UnitEnum
     */
    public static function ofEnum(string|object $enumClass = \UnitEnum::class): Bag
    {
        if (!\is_a($enumClass, \UnitEnum::class, true))
            throw new \InvalidArgumentException("$enumClass must be a \UnitEnum");

        if (!\is_string($enumClass))
            $enumClass = \get_class($enumClass);

        return new class(
            ObjectContainers::create(),
            $enumClass
        ) extends BagWithStorage
        {
            public function __construct(
                Container $storage,
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
                return Iterables::keys(parent::getIterator());
            }
        };
    }

    /**
     * A bag able to store \BackedEnum instances.
     * 
     * Internally it uses a {@see Bags::toArrayKeys()} Bag to assign the backed 
     * string|int value of an enum value.
     *
     * @template T of \BackedEnum
     * @param string|T $enumClass
     *            The backed enum class of the elements to store.
     *            It may be a string class name of T or a T instance.
     * @return Bag<T> A new Bag.
     * @link https://www.php.net/manual/en/class.backedenum.php \BackedEnum
     */
    public static function ofBackedEnum(string|object $enumClass = \BackedEnum::class): Bag
    {
        if (!\is_a($enumClass, \BackedEnum::class, true))
            throw new \InvalidArgumentException("$enumClass must be a \BackedEnum");

        /** @var Bag<T> */
        return self::ofEnum($enumClass);
    }

    /**
     * Decorates a Bag to be unmodifiable.
     *
     * Call to a mutable method of the bag will throws a {@see Exception\UnmodifiableBagException}.
     *
     * @template T
     * 
     * @param Bag<T> $bag
     *            A bag to decorate.
     * @return BagWithStorage<T> The backed unmodifiable bag.
     */
    public static function unmodifiable(Bag $bag): Bag
    {
        return new class($bag)
        extends BagWithStorage
        implements IsUnmodifiable
        {
            use UnmodifiableArrayAccessContainer,
                UnmodifiableContainerPutMethods;
        };
    }

    /**
     * Gets the null pattern unmodifiable Bag.
     *
     * The value is a singleton and may be compared with the `===` operator.
     * 
     * @return BagWithStorage<void> The unique null pattern Bag.
     */
    public static function null(): Bag
    {
        static $null = self::unmodifiable(self::arrayKeys());
        return $null;
    }

    // ========================================================================
    // OPERATIONS

    /**
     * Checks if two Bags contains the same items.
     * 
     * @param Bag<mixed> $a First Bag.
     * @param Bag<mixed> $b Second Bag.
     * @return bool true if the two Bags contains the same items, false otherwise.
     */
    public static function equals(Bag $a, Bag $b): bool
    {
        if ($a === $b)
            return true;
        if (\count($a) !== \count($b))
            return false;
        foreach ($a as $item) {
            if ($b[$item] !== $a[$item])
                return false;
        }
        return true;
    }

    /**
     * Checks whether the items of a Bag are part of another Bag.
     * 
     * @param Bag<mixed> $searchFor The items to search for.
     * @param Bag<mixed> $inside The Bag to search in.
     * 
     * @return bool true if all the items of $searchFor are inside the Bag `$inside`.
     */
    public static function isIncludedIn(Bag $searchFor, Bag $inside): bool
    {
        if ($searchFor === $inside)
            return true;
        if (\count($searchFor) > \count($inside))
            return false;
        foreach ($searchFor as $item) {
            if ($searchFor[$item] > $inside[$item])
                return false;
        }
        return true;
    }
}
