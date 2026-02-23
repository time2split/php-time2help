<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\Class\ToArray; //phpstan

/**
 * An implementation of `ToArray::toArray`
 * transforming the instance's entries into an array.
 * 
 * ```
 * function toArray(): array
 * {
 *     return iterator_to_array($this);
 * }
 * ```
 *
 * @var iterable<K,V> $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 * 
 * @phpstan-require-implements ToArray<K,V>&(\Traversable<K,V>|array<K,V>)
 */
trait IteratorToArray
{
    /**
     * @return array<V>
     */
    #[\Override]
    public function toArray(): array
    {
        return \iterator_to_array($this);
    }
}
