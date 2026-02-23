<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\Class\Clearable; // phpstan

/**
 * An implementation of a Container using an internal storage.
 * 
 * (It must have a property: `Clearable&\Countable&\IteratorAggregate $storage`)
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 * 
 * @phpstan-property Clearable&\Countable&\IteratorAggregate<K,V> $storage
 * @phpstan-require-implements Clearable&\Countable&\IteratorAggregate<K,V>
 */
trait ContainerWithStorage
{
    /**
     * @use IteratorAggregateWithStorage<K,V>
     */
    use
        ClearableWithStorage,
        CountableWithStorage,
        IteratorAggregateWithStorage;
}
