<?php

namespace Time2Split\Help\Container\Class;

use Time2Split\Help\Container\ArrayContainer;

/**
 * Transform into an array.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 *
 * @template K of int|string
 * @template V
 */
interface ToArray
{
    /**
     * Transforms the object into an array
     * 
     * @phpstan-return array<K,V>
     * 
     * @return array<V>
     */
    public function toArray(): array;

    /**
     * Transforms the object into an ArrayContainer
     * 
     * @phpstan-return ArrayContainer<K,V>
     * 
     * @return ArrayContainer<V>
     */
    public function toArrayContainer(): ArrayContainer;
}
