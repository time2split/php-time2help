<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Closure;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\Trait\ContainerMapKey;
use Time2Split\Help\Container\_internal\ArrayContainerImpl;
use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\Trait\UnmodifiableContainerAA;
use Time2Split\Help\Container\Trait\UnmodifiableElementsUpdating;
use Time2Split\Help\Exception\UnmodifiableException;
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
     * @template K of array-key
     * @template V
     * 
     * @param iterable<K,V> ...$arrays
     *       The initial array contents
     * @return ArrayContainer<K,V> A new array container.
     */
    static public function create(iterable ...$arrays): ArrayContainer
    {
        $array = \iterator_to_array(Iterables::append(...$arrays));
        return new class($array) extends ArrayContainerImpl {

            #[\Override]
            public function copy(): static
            {
                return new static($this->storage);
            }
        };
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
     * @template KMAP of array-key
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
        return new class($array, $mapKey)
        extends ArrayContainerImpl
        {
            /**
             * @use ContainerMapKey<K,KMAP,V>
             */
            use ContainerMapKey;

            /**
             * @param array<KMAP,V> $storage
             */
            public function __construct(
                array $storage,
                callable $mapKey,
            ) {
                parent::__construct($storage);
                $this->setMapKey($mapKey);
            }

            #[\Override]
            public function copy(): static
            {
                $ret = new self(
                    $this->storage,
                    $this->mapKey,
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
     * @template K of array-key
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
            /**
             * @use UnmodifiableContainerAA<K,V>
             * @use UnmodifiableElementsUpdating<V>
             */
            use
                UnmodifiableContainerAA,
                UnmodifiableElementsUpdating;

            #[\Override]
            public function __call(string $name, array $arguments): mixed
            {
                throw new UnmodifiableException();
            }
        };
    }
}
