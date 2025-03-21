<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\ArrayContainers;

/**
 * An implementation of a Container using an internal array storage.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 * 
 * @var array<K,V> $storage The internal storage must be defined into the class.
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

    /**
     * @return array<K,V>
     */
    #[\Override]
    public function toArray(): array
    {
        return $this->storage;
    }

    /**
     * @return ArrayContainer<K,V>
     */
    #[\Override]
    public function toArrayContainer(): ArrayContainer
    {
        /* @phpstan-ignore return.type */
        return ArrayContainers::create($this->storage);
    }
}
