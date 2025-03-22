<?php

namespace Time2Split\Help\Container;

/**
 * Remove all entries of a container.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\interface
 */
interface Clearable
{
    /**
     * Removes all entries from the container.
     */
    public function clear(): void;
}
