<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Classes\IsUnmodifiable;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\Trait\UnmodifiableArrayAccessContainer;

/**
 * Factories and functions for ArrayContainer instances.
 * 
 * @author Olivier Rodriguez (zuri)
 */
final class ObjectContainers
{
    use NotInstanciable;

    public static function create(iterable ...$iterables)
    {
        $ret = new class() extends ObjectContainer {};
        $ret->updateEntries(...$iterables);
        return $ret;
    }

    public static function null(): ObjectContainer
    {
        static $null = new class()
        extends ObjectContainer
        implements IsUnmodifiable
        {
            use UnmodifiableArrayAccessContainer;
        };
        return $null;
    }

    public static function unmodifiable(ObjectContainer $subject): ObjectContainer
    {
        return new class($subject)
        extends ObjectContainer
        implements IsUnmodifiable
        {
            use UnmodifiableArrayAccessContainer;
            public function __construct(ObjectContainer $subject)
            {
                $this->storage = &$subject->storage;
            }
        };
    }
}
