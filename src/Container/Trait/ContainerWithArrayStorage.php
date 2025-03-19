<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\ArrayContainers;

/**
 * An implementation of a Container using an internal array storage.
 * 
 * ```
 * public function offsetExists(mixed $offset): bool
 * {
 *     return \array_key_exists($offset, $this->storage);
 * }
 * public function clear(): void
 * {
 *     $this->storage = [];
 * }
 * public function toArray(): array
 * {
 *     return $this->storage;
 * }
 * ```
 * @var array $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait ContainerWithArrayStorage
{
    use
        CountableWithStorage,
        IteratorAggregateWithArrayStorage;

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

    #[\Override]
    public function toArrayContainer(): ArrayContainer
    {
        return ArrayContainers::create($this->storage);
    }
}
