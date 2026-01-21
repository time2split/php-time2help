<?php

namespace Time2Split\Help\Container\Trait;

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
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * 
 * @template K
 * @template V
 */
trait IteratorToArrayOfEntries
{
    /**
     * @return list<Entry<K,V>>
     */
    #[\Override]
    public function toArray(): array
    {
        return \iterator_to_array(Entry::arrayToListOfEntries($this));
    }
}
