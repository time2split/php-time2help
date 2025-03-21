<?php

namespace Time2Split\Help\Classes;

/**
 * The class has a null representant.
 * 
 * The null representant must be unique and immutable.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 * 
 * @template K
 * @template V
 */
interface GetNullInstance
{
    /**
     * Get the null instance.
     * @return self<K,V>
     */
    public static function null(): self;
}
