<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use ArrayAccess;

/**
 * An implementation of a Container using an internal storage.
 * 
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 */
trait ContainerWithStorage
{
    /**
     * @use IteratorAggregateWithStorage<K,V>
     * @use ToArrayToArrayContainer<K,V>
     * @use ToArrayWithStorage<K,V>
     */
    use
        ClearableWithStorage,
        CountableWithStorage,
        IteratorAggregateWithStorage,
        ToArrayToArrayContainer,
        ToArrayWithStorage;
}
