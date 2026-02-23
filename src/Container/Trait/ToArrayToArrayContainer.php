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
 *     return ArrayContainers::create($this->toArray());
 * }
 * ```
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K of array-key
 * @template V
 * 
 * @phpstan-require-implements ToArray<K,V>
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
