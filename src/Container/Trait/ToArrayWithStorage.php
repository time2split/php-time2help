<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ToArray;

/**
 * An implementation of `ToArray::toArrayContainer`.
 * 
 * 
 * ```
 * function toArray(): ArrayContainer
 * {
 *     return $this->storage->toArray();
 * }
 * ```
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 * @var ToArray<K,V> $storage The internal storage must be defined into the class.
 */
trait ToArrayWithStorage
{
    /**
     * @return array<K,V>
     */
    #[\Override]
    public function toArray(): array
    {
        return $this->storage->toArray();
    }
}
