<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\Container;

/**
 * An implementation of a Container using an internal Container storage.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 */
trait ContainerWithContainerStorage
{
    /**
     * @use IteratorAggregateWithStorage<K,V>
     * @use IteratorToArray<K,V>
     * @use IteratorToArrayContainer<K,V>
     */
    use
        ClearableWithStorage,
        CountableWithStorage,
        IteratorAggregateWithStorage,
        IteratorToArray,
        IteratorToArrayContainer;
}
