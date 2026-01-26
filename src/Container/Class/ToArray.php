<?php

namespace Time2Split\Help\Container\Class;

use Time2Split\Help\Container\ArrayContainer;

/**
 * Transform into an array.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 *
 * @template K
 * @template V
 */
interface ToArray
{
    /**
     * Transforms the object into an array
     * @return array<K,V>
     */
    public function toArray(): array;

    /**
     * Transforms the object into an ArrayContainer
     * @return ArrayContainer<K,V>
     */
    public function toArrayContainer(): ArrayContainer;
}
