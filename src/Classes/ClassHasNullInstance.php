<?php

namespace Time2Split\Help\Classes;

/**
 * The class has a null representant.
 * 
 * The null representant must be unique and immutable
 * (implements {@see IsUnodifiable}).
 *
 * @see GetUnmodifiable
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 * 
 * @template T
 */
interface ClassHasNullInstance
{
    /**
     * Get the null instance.
     * @phpstan-return T
     */
    public static function null(): mixed;
}
