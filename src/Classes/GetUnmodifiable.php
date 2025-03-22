<?php

namespace Time2Split\Help\Classes;

use \Time2Split\Help\Exception\UnmodifiableException;

/**
 * Create a backed unmodifiable instance.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
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
     * @return T
     *      A wrapper arround the object.
     * @throws UnmodifiableException
     *      If a mutable method is called.
     */
    public function unmodifiable(): mixed;
}
