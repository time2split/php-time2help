<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\Container;

/**
 * An implementation of a Container using an internal array storage.
 * 
 * @var array $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait ContainerWithArrayStorage
{
    use
        CountableWithStorage,
        IteratorAggregateWithArrayStorage,
        ToArrayToArrayContainer;

    #[\Override]
    public function clear(): void
    {
        $this->storage = [];
    }

    #[\Override]
    public function toArray(): array
    {
        return $this->storage;
    }
}
