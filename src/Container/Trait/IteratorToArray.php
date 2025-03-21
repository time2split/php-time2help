<?php

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation of `ToArray::toArray`.
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
 */
trait IteratorToArray
{
    #[\Override]
    public function toArray(): array
    {
        return \iterator_to_array($this);
    }
}
