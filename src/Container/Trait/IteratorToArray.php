<?php

namespace Time2Split\Help\Container\Trait;

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
 * @var \Traversable $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 * 
 * @template K
 * @template V
 */
trait IteratorToArray
{
    /**
     * @return array<K,V>
     */
    #[\Override]
    public function toArray(): array
    {
        return \iterator_to_array($this);
    }
}
