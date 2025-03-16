<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\_internal\BagWithStorage;
use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\Container;
use Time2Split\Help\Container\ObjectContainer;
use Time2Split\Help\Container\Trait\UnmodifiableArrayAccess;

final class Bags
{
    use NotInstanciable;

    /**
     * Provides a bag storing items as array keys.
     *
     * This bag is only convenient for data types that can fit as array keys.
     *
     * @return Bag<string|int> A new bag.
     */
    public static function arrayKeys(): Bag
    {
        return new class(new ArrayContainer())
        extends BagWithStorage {
            #[\Override]
            public function getIterator(): \Traversable
            {
                foreach ($this->storage as $item => $v)
                    yield $item;
            }
        };
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
     * @param \Closure $toKey
     *            Map an input item to a valid key.
     * @param \Closure $fromKey
     *            Retrieves the base object from the array key.
     * @return Bag<mixed> A new Bag.
     */
    public static function toArrayKeys(\Closure $toKey, \Closure $fromKey): Bag
    {
        return new class($toKey, $fromKey, self::arrayKeys())
        extends BagWithStorage
        {
            public function __construct(
                private readonly \Closure $toKey,
                private readonly \Closure $fromKey,
                Bag $storage
            ) {
                parent::__construct($storage);
            }

            public function offsetSet(mixed $offset, mixed $value): void
            {
                $this->storage[($this->toKey)($offset)] =  $value;
            }

            public function offsetGet(mixed $offset): int
            {
                return $this->storage[($this->toKey)($offset)];
            }

            public function getIterator(): \Traversable
            {
                foreach ($this->storage as $k => $v)
                    yield $k => ($this->fromKey)($v);
            }

            public function copy(): static
            {
                return new self(
                    $this->toKey,
                    $this->fromKey,
                    $this->storage->copy()
                );
            }
        };
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
            new ObjectContainer,
            $enumClass
        ) extends BagWithStorage
        {
            public function __construct(
                Container $storage,
                private string $enumClass
            ) {
                parent::__construct($storage);
            }

            public function offsetSet(mixed $offset, mixed $value): void
            {
                if (!\is_a($offset, $this->enumClass, true)) {
                    $have = \get_class($offset);
                    throw new \InvalidArgumentException("The item to assign must be a $this->enumClass (have $have)");
                }
                parent::offsetSet($offset, $value);
            }

            public function copy(): static
            {
                return new self(
                    $this->storage->copy(),
                    $this->enumClass
                );
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
     * @return Bag<T> The backed unmodifiable bag.
     */
    public static function unmodifiable(Bag $bag): Bag
    {
        return new class($bag) extends BagWithStorage
        {
            use UnmodifiableArrayAccess;
        };
    }

    /**
     * @var Bag<void>
     */
    private static Bag $null;

    /**
     * Gets the null pattern unmodifiable Bag.
     *
     * The value is a singleton and may be compared with the `===` operator.
     * 
     * @return Bag<void> The unique null pattern Bag.
     */
    public static function null(): Bag
    {
        return self::$null ??= new class(new ArrayContainer)
        extends BagWithStorage
        {
            use UnmodifiableArrayAccess;

            #[\Override]
            public function copy(): static
            {
                return $this;
            }
        };
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
    public static function includedIn(Bag $searchFor, Bag $inside): bool
    {
        if ($searchFor === $inside)
            return true;
        if (\count($searchFor) > \count($inside))
            return false;
        foreach ($searchFor as $item) {
            if ($inside[$item] < $searchFor[$item])
                return false;
        }
        return true;
    }
}
