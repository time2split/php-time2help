<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\ArrayContainers;
use Time2Split\Help\Container\Entry;

/**
 * An implementation of `ToArray::toArrayContainer`.
 * 
 * ```
 * public function toArrayContainer(): ArrayContainer
 * {
 *     return ArrayContainers::create($this->toArray());
 * }
 * ```
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 * 
 * @template K
 * @template V
 */
trait ToArrayToArrayContainer
{
    /**
     * @return ArrayContainer<K,V>
     */
    #[\Override]
    public function toArrayContainer(): ArrayContainer
    {
        return ArrayContainers::create($this->toArray());
    }
}
