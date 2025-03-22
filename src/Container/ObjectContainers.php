<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Classes\IsUnmodifiable;
use Time2Split\Help\Classes\NotInstanciable;

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
        static $null = self::unmodifiable(self::create());
        return $null;
    }

    public static function unmodifiable(ObjectContainer $subject): ObjectContainer&IsUnmodifiable
    {
        return new class($subject)
        extends ObjectContainer
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
