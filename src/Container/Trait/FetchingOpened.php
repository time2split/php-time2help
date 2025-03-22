<?php

namespace Time2Split\Help\Container\Trait;

use Closure;
use Time2Split\Help\Container\ContainerBase;

/**
 * Methods for simple querying.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 * @template C of ContainerBase<K,V>
 */
trait FetchingOpened
{
    /**
     * Whether another container have the same contents according to
     * a comparison strategy.
     * 
     * @param C $other The other container.
     * @param bool|Closure $strictOrEquals The comparison strategy.
     *  - `true`: The `===` operator is used.
     *  - `false`: The `==` operator is used.
     *  - `strictOrEquals(mixed $a, mixed $b):bool`
     * 
     *      Returns true if the items $a and $b are equals.
     * 
     * @return bool
     * - `true`: the other container contains exactly the same elements.
     * - `false`: otherwise.
     */
    abstract public function equals(
        self $other,
        bool|Closure $strictOrEquals = false
    ): bool;

    /**
     * Whether another container included the contents of $this according to
     * a comparison strategy.
     * 
     * @param C $other The other container.
     * @param bool|Closure $strictOrEquals The comparison strategy.
     *  - `true`: The `===` operator is used.
     *  - `false`: The `==` operator is used.
     *  - `strictOrEquals(mixed $a, mixed $b):bool`
     * 
     *      Returns true if the items $a and $b are equals.
     * @param $strictInclusion
     *  - `true`: The inclusion must be strict (the other container have more elements)
     *  - `false`: The inclusion is not necessary strict (the other container may be equal)
     * 
     * @return bool
     * - `true`: the other container contains the same elements (and maybe more).
     * - `false`: otherwise.
     */
    abstract public function isIncludedIn(
        self $other,
        bool|Closure $strictOrEquals = false,
        bool $strictInclusion = false,
    ): bool;

    // ========================================================================

    /**
     * @param C $other
     */
    protected final function isStrictlyIncludedIn(
        self $other,
        bool|Closure $strictOrEquals = false,
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
