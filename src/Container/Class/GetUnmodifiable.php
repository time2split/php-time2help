<?php

namespace Time2Split\Help\Container\Class;

use \Time2Split\Help\Exception\UnmodifiableException;

/**
 * Create a backed unmodifiable instance.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template T
 */
interface GetUnmodifiable
{
    /**
     * Create a backed unmodifiable instance.
     * 
     * The original instance stay inside the unmodifiable instance,
     * thus it still can be modified externally.
     * 
     * Any operation modifying the content of the instance must throws an exception.
     * 
     * @phpstan-return T&IsUnmodifiable
     * @return IsUnmodifiable
     *      A {@see IsUnmodifiable} wrapper arround the object.
     *      The instance must throw a {@see UnmodifiableException}
     *      if a writing method is called.
     */
    public function unmodifiable(): IsUnmodifiable;
}
