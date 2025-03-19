<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\ArrayContainers;
use Time2Split\Help\Container\Entry;

/**
 * An implementation of `ToArray::toArray` which return a list of Entry.
 * 
 * ```
 * function toArray(): array
 * {
 *      return iterator_to_array(Entry::toListOfEntries($this));
 * }
 * ```
 *
 * @var \Traversable $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 */
trait IteratorToArrayOfEntries
{
    #[\Override]
    public function toArray(): array
    {
        return iterator_to_array(Entry::arrayToListOfEntries($this));
    }
}
