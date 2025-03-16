<?php

namespace Time2Split\Help\Container;

/**
 * Transform into an array.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 */
interface ToArray
{
    /**
     * Transforms the object into an array
     */
    public function toArray(): array;

    /**
     * Transforms the object into an ArrayContainer
     */
    public function toArrayContainer(): ArrayContainer;
}
