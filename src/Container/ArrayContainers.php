<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Closure;
use Time2Split\Help\Classes\IsUnmodifiable;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\Trait\ContainerMapKey;
use Time2Split\Help\Container\Trait\IteratorToArrayOfEntries;
use Time2Split\Help\Container\Trait\UnmodifiableArrayAccessContainer;
use Time2Split\Help\Iterables;

/**
 * Factories and functions for ArrayContainer instances.
 * 
 * @author Olivier Rodriguez (zuri)
 */
final class ArrayContainers
{
    use NotInstanciable;

    /**
     * 
     * @param iterable ...$arrays
     *       The initial array contents
     * @return ArrayContainer A new array container.
     */
    static public function create(iterable ...$arrays)
    {
        $array = \iterator_to_array(Iterables::append(...$arrays));
        return new class($array) extends ArrayContainer {};
    }

    /**
     * Provides an array container storing arbitrary items as array keys.
     * 
     * This Set can be used when an element can be associated with a unique array key identifier.
     *
     * This class permits to handle more types of values and not just array keys.
     * It makes a bijection between a valid array key and an element.
     *
     * @template K
     * @template KMAP
     * @template V
     * 
     * @param Closure(K):KMAP $mapKey
     *       Map an input item to a valid key.
     * @param iterable<K,V> ...$iterables
     *       The initial array contents
     * @return ArrayContainer<K,V> A new array container.
     */
    static public function toArrayKeys(Closure $mapKey, iterable ...$iterables): ArrayContainer
    {
        $array = \iterator_to_array(Iterables::append(...$iterables));

        return new class($mapKey, $array) extends ArrayContainer {
            use ContainerMapKey,
                IteratorToArrayOfEntries;
            public function __construct(
                callable $mapKey,
                array $storage
            ) {
                parent::__construct($storage);
                $this->setMapKey($mapKey);
            }
            #[\Override]
            public function copy(): static
            {
                $ret = new self(
                    $this->mapKey,
                    $this->storage
                );
                $ret->copyMapKeyInternals($this);
                return $ret;
            }
            #[\Override]
            public function toArrayContainer(): ArrayContainer
            {
                return $this->copy();
            }
        };
    }

    static public function null(): ArrayContainer
    {
        static $null = new class([])
        extends ArrayContainer
        implements IsUnmodifiable
        {
            use UnmodifiableArrayAccessContainer;
        };
        return $null;
    }

    static public function unmodifiable(ArrayContainer $subject): ArrayContainer
    {
        return new class($subject)
        extends ArrayContainer
        implements IsUnmodifiable
        {
            use UnmodifiableArrayAccessContainer;
            public function __construct(ArrayContainer $subject)
            {
                $this->storage = &$subject->storage;
            }
        };
    }
}
