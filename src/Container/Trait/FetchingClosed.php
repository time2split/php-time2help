<?php

namespace Time2Split\Help\Container\Trait;

/**
 * Methods for simple querying without external comparison function.
 *
 * It must be used in the first effective object class declaration to set
 * properly the `self` typyng contraints.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait FetchingClosed
{
    abstract public function equals(
        self $other,
    ): bool;

    abstract private function isIncludedIn(
        self $other,
        bool $strictInclusion = false,
    ): bool;

    // ========================================================================

    protected final function isStrictlyIncludedIn(
        self $other
    ): bool {

        if ($this === $other)
            return false;

        $ca = $this->count();
        $cb = $other->count();

        if ($ca === $cb)
            return false;

        return $this->isIncludedIn($other, false);
    }
}
