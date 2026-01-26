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
     * @template O
     * @template V
     * 
     * @param iterable<O,V> $iterables[]
     * @return ObjectContainer<O,V>
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
     * @template O
     * @template V
     * 
     * @param ObjectContainer<O,V> $subject
     * @return IsUnmodifiable&ObjectContainer<O,V>
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
                Trait\UnmodifiableElementsUpdating,
                Trait\UnmodifiableClearable;

            /**
             * @param ObjectContainer<O,V> $subject
             */
            public function __construct(ObjectContainer $subject)
            {
                $this->storage = &$subject->storage;
            }
        };
    }
}
