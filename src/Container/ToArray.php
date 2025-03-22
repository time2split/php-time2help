<?php

namespace Time2Split\Help\Container;

/**
 * Transform into an array.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class\interface
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
