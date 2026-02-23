<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\ArrayContainers;
use Time2Split\Help\Container\Class\ToArray; //phpstan

/**
 * An implementation of `ToArray::toArrayContainer`.
 * 
 * ```
 * public function toArrayContainer(): ArrayContainer
 * {
 *     return ArrayContainers::create($this->getIterator());
 * }
 * ```
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 * 
 * @phpstan-require-implements iterable<K,V>&ToArray<K,V>
 */
trait IteratorToArrayContainer
{
    /**
     * @return ArrayContainer<K,V>
     */
    #[\Override]
    public function toArrayContainer(): ArrayContainer
    {
        return ArrayContainers::create($this);
    }
}
