<?php

declare(strict_types=1);

namespace Time2Split\Help\Memory;

use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\Set;

/**
 * A memoizer of \UnitEnum cases.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\memory
 * 
 * @template E of \UnitEnum
 */
interface EnumSetMemoizer extends Memoizer
{
    /**
     * Memoize a set of \UnitEnum cases.
     * 
     * @param E ...$cases
     *      The enum cases to memoize.
     * @return Set<E>
     *      The set of cases.
     * @throws \InvalidArgumentException
     *      If one enum case is of the wrong type.
     */
    function memoize(\UnitEnum ...$cases): Set&IsUnmodifiable;

    /**
     * Gets the allowed enum class.
     * 
     * @phpstan-return class-string
     * @return string
     *      The enum class name.
     */
    function getEnumClass(): string;
}
