<?php

declare(strict_types=1);

namespace Time2Split\Help;

use Time2Split\Help\_private\Set\BaseSet;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\_private\Set\SetDecorator;
use Time2Split\Help\_private\Set\SetWithStorage;
use Time2Split\Help\Trait\NullArrayAccess;
use Time2Split\Help\Trait\UnmodifiableArrayAccess;

/**
 * Factories and functions on set.
 * 
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 */
final class Sets
{
    use NotInstanciable;

    /**
     * Provides a set storing items as array keys.
     *
     * This set is only convenient for data types that can fit as array keys.
     *
     * @return Set<string|int> A new set.
     */
    public static function arrayKeys(): Set
    {
        return new class() extends SetWithStorage {};
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
     * @param \Closure $toKey
     *            Map an input item to a valid key.
     * @param \Closure $fromKey
     *            Retrieves the base object from the array key.
     * @return Set<mixed> A new Set.
     */
    public static function toArrayKeys(\Closure $toKey, \Closure $fromKey): Set
    {
        return new class($toKey, $fromKey) extends SetDecorator
        {
            public function __construct(
                private readonly \Closure $toKey,
                private readonly \Closure $fromKey,
                ?Set $decorate = null,
            ) {
                parent::__construct($decorate ?? Sets::arrayKeys());
            }

            public function offsetSet(mixed $offset, mixed $value): void
            {
                $this->decorate[($this->toKey)($offset)] = $value;
            }

            public function offsetGet(mixed $offset): bool
            {
                return $this->decorate[($this->toKey)($offset)];
            }

            public function getIterator(): \Traversable
            {
                foreach ($this->decorate as $k => $v)
                    yield $k => ($this->fromKey)($v);
            }

            public function copy(): static
            {
                return new self(
                    $this->toKey,
                    $this->fromKey,
                    $this->decorate->copy()
                );
            }
        };
    }

    private static function copySplObjectStorage(\SplObjectStorage $storage)
    {
        $ret = new \SplObjectStorage();
        $ret->addAll($storage);
        return $ret;
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
            new \SplObjectStorage(),
            self::copySplObjectStorage(...),
            $enumClass
        ) extends SetWithStorage {

            public function __construct(
                $storage,
                ?callable $copyStorage,
                private string $enumClass
            ) {
                parent::__construct($storage, $copyStorage);
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
                    $this->storageCopy(),
                    $this->copyStorage,
                    $this->enumClass
                );
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
     * Call to a mutable method of the set will throws a {@see Exception\UnmodifiableSetException}.
     *
     * @template T
     * 
     * @param Set<T> $set
     *            A set to decorate.
     * @return Set<T> The backed unmodifiable set.
     */
    public static function unmodifiable(Set $set): Set
    {
        return new class($set) extends SetDecorator
        {
            use UnmodifiableArrayAccess;
        };
    }

    /**
     * @var Set<void>
     */
    private static Set $null;

    /**
     * Gets the null pattern unmodifiable set.
     *
     * The value is a singleton and may be compared with the `===` operator.
     * 
     * @return Set<void> The unique null pattern set.
     */
    public static function null(): Set
    {
        return self::$null ??= new class() extends BaseSet implements \IteratorAggregate
        {
            use NullArrayAccess;
            public final function offsetGet(mixed $offset): bool
            {
                return false;
            }

            public function copy(): static
            {
                return $this;
            }
        };
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
    public static function includedIn(Set $searchFor, Set $inside): bool
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
