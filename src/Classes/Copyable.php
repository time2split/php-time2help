<?php

namespace Time2Split\Help\Classes;

/**
 * Create a new instance that is a (deep) copy of itself.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 */
interface Copyable
{
    /**
     * Creates a (deep) copy of itself.
     * 
     * @return T A new instance, copy of the initial object.
     */
    public function copy(): static;
}
