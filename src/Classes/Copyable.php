<?php

namespace Time2Split\Help\Classes;

/**
 * Create a (deep) copy of itself.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 * 
 * @template T
 */
interface Copyable
{
    /**
     * Creates a (deep) copy of itself.
     * 
     * The created instance must be fully independant from the original one.
     */
    public function copy(): mixed;
}
