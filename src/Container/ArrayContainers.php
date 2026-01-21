<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Closure;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\Trait\ContainerMapKey;
use Time2Split\Help\Container\Trait\IteratorToArrayOfEntries;
use Time2Split\Help\Container\_internal\ArrayContainerImpl;
use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Iterables;

/**
 * Factories and functions for ArrayContainer instances.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\php
 */
final class ArrayContainers
{
    use NotInstanciable;

    /**
     * @template K
     * @template V
     * 
     * @param iterable<K,V> ...$arrays
     *       The initial array contents
     * @return ArrayContainer<K,V> A new array container.
     */
    static public function create(iterable ...$arrays): ArrayContainer
    {
        $array = \iterator_to_array(Iterables::append(...$arrays));
        return new class($array) extends ArrayContainerImpl {};
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

        /**
         * @extends ArrayContainerImpl<K,V>
         */
        return new class($mapKey, $array) extends ArrayContainerImpl {

            /**
             * @use ContainerMapKey<K,KMAP,V>
             * @use IteratorToArrayOfEntries<K,V>
             */
            use ContainerMapKey,
                IteratorToArrayOfEntries;

            /**
             * @param array<KMAP,V> $storage
             */
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
        };
    }

    /*
    static public function null(): ArrayContainer
    {
        static $null = self::unmodifiable(ArrayContainers::create());
        return $null;
    }
    //*/

    /**
     * @template K
     * @template V
     * 
     * @param ArrayContainer<K,V> $subject
     * @return isUnmodifiable&ArrayContainer<K,V>
     */
    static public function unmodifiable(ArrayContainer $subject): ArrayContainer&IsUnmodifiable
    {
        return new class($subject)
        extends ArrayContainerImpl
        implements IsUnmodifiable
        {
            use
                Trait\UnmodifiableContainerAA,
                Trait\UnmodifiableElementsUpdating;

            /**
             * @param ArrayContainer<K,V> $subject
             */
            public function __construct(ArrayContainer $subject)
            {
                $this->storage = &$subject->storage;
            }

            #[\Override]
            public function copy(): static
            {
                return $this;
            }
        };
    }
}
