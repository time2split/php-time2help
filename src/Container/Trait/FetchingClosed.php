<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ContainerBase;

/**
 * Methods for simple querying without external comparison function.
 *
 * It must be used in the first effective object class declaration to set
 * properly the `self` typyng contraints.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 * @template C of ContainerBase<K,V>
 */
trait FetchingClosed
{
    /**
     * @param C $other
     */
    abstract public function equals(
        ContainerBase $other,
    ): bool;

    /**
     * @param C $other
     */
    abstract public function isIncludedIn(
        ContainerBase $other,
        bool $strictInclusion = false,
    ): bool;

    // ========================================================================

    /**
     * @param C $other
     */
    protected final function isStrictlyIncludedIn(
        ContainerBase $other
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
