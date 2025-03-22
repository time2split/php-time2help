<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ContainerBase;

/**
 * Methods for simple querying using an internal comparison strategy.
 * 
 * Some containers cannot use an external comparison strategy like {@see FetchingOpened}.
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
     * @param C $other The other container.
     * 
     * @return bool
     * - `true`: the other container contains exactly the same elements.
     * - `false`: otherwise.
     */
    abstract public function equals(
        ContainerBase $other,
    ): bool;

    /**
     * @param C $other The other container.
     *  @return
     * - `true`: the other container have the same elements.
     * - `false`: otherwise.
     * 
     *  @return bool
     * - `true`: the other container contains the same elements (and maybe more).
     * - `false`: otherwise.
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
