<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\ArrayContainers;

/**
 * An implementation of a Container using an internal array storage.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 * 
 * @property array $storage The internal storage must be defined into the class definition.
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
