<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation of a Container using an internal array storage.
 * 
 * (It must have a property: `array $storage`)
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 * 
 * @phpstan-property array<K,V> $storage
 * @phpstan-require-implements \Countable&\IteratorAggregate<K,V> 
 */
trait ContainerWithArrayStorage
{
    /**
     * @use IteratorAggregateWithArrayStorage<K,V>
     */
    use
        CountableWithStorage,
        IteratorAggregateWithArrayStorage;

    #[\Override]
    public function clear(): void
    {
        $this->storage = [];
    }
}
