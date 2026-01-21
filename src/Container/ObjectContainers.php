<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\_internal\ObjectContainerImpl;
use Time2Split\Help\Container\Class\IsUnmodifiable;

/**
 * Factories and functions for ArrayContainer instances.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\php
 */
final class ObjectContainers
{
    use NotInstanciable;

    /**
     * @template K
     * @template V
     * 
     * @param iterable<K,V> $iterables[]
     * @return ObjectContainer<K,V>
     */
    public static function create(iterable ...$iterables): ObjectContainer
    {
        $ret = new class() extends ObjectContainerImpl {};
        $ret->updateEntries(...$iterables);
        return $ret;
    }

    /*
    public static function null(): ObjectContainer
    {
        static $null = self::unmodifiable(self::create());
        return $null;
    }
    //*/

    /**
     * @template K
     * @template V
     * 
     * @param ObjectContainer<K,V> $subject
     * @return IsUnmodifiable&ObjectContainer<K,V>
     */
    public static function unmodifiable(ObjectContainer $subject): ObjectContainer&IsUnmodifiable
    {
        return new class($subject)
        extends ObjectContainerImpl
        implements IsUnmodifiable
        {
            use
                Trait\UnmodifiableContainerAA,
                Trait\UnmodifiableArrayAccessUpdating,
                Trait\UnmodifiableContainerPutMethods,
                Trait\UnmodifiableClearable;
            public function __construct(ObjectContainer $subject)
            {
                $this->storage = &$subject->storage;
            }
        };
    }
}
