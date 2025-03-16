<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ArrayContainer;

/**
 * An implementation of `ToArray::toArrayContainer`.
 * 
 * @var ToArray $storage The internal storage must be defined into the class.
 * 
 * ```
 * function toArray(): ArrayContainer
 * {
 *     return $this->storage->toArray();
 * }
 *
 * function toArrayContainer(): ArrayContainer
 * {
 *     return $this->storage->toArrayContainer();
 * }
 * ```
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 */
trait ToArrayWithStorage
{
    #[\Override]
    public function toArray(): array
    {
        return $this->storage->toArray();
    }

    #[\Override]
    public function toArrayContainer(): ArrayContainer
    {
        return $this->storage->toArrayContainer();
    }
}
