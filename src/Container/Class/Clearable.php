<?php

namespace Time2Split\Help\Container\Class;

/**
 * Remove all entries of a container.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 */
interface Clearable
{
    /**
     * Removes all entries from the container.
     */
    public function clear(): void;
}
