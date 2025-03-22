<?php

namespace Time2Split\Help\Container\Trait;

/**
 * Methods for simple querying.
 *
 * It must be used in the first effective object class declaration to set
 * properly the `self` typyng contraints.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait FetchingOpened
{
    abstract public function equals(
        self $other,
        bool|callable $strictOrEquals = false
    ): bool;

    abstract public function isIncludedIn(
        self $other,
        bool|callable $strictOrEquals = false,
        bool $strictInclusion = false,
    ): bool;

    // ========================================================================

    protected final function isStrictlyIncludedIn(
        self $other,
        bool|callable $strictOrEquals = false,
    ): bool {

        if ($this === $other)
            return false;

        $ca = $this->count();
        $cb = $other->count();

        if ($ca === $cb)
            return false;

        return $this->isIncludedIn($other, $strictOrEquals, false);
    }
}
