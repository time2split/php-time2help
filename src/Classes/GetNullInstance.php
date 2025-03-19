<?php

namespace Time2Split\Help\Classes;

/**
 * The class has a null representant.
 * 
 * The null representant must be unique and immutable.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 */
interface GetNullInstance
{
    /**
     * Get the null instance.
     */
    public static function null(): self;
}
