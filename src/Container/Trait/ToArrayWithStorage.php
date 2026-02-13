<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\Class\ToArray; //phpstan

/**
 * An implementation of `ToArray::toArrayContainer`.
 * 
 * (It must have a property: `ToArray $storage`)
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
 * 
 * @phpstan-property ToArray<K,V> $storage
 * @phpstan-require-implements ToArray<K,V>
 */
trait ToArrayWithStorage
{
    /**
     * @return array
     */
    #[\Override]
    public function toArray(): array
    {
        return $this->storage->toArray();
    }
}
