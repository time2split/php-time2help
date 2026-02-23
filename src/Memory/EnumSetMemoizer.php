<?php

declare(strict_types=1);

namespace Time2Split\Help\Memory;

use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\Set;

/**
 * A memoizer of UnitEnum cases.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\memory
 * 
 * @template E of \UnitEnum
 * @extends Memoizer<list<E>,Set<E>>
 */
interface EnumSetMemoizer extends Memoizer
{
    /**
     * Memoize a set of UnitEnum cases.
     * 
     * @param \UnitEnum ...$cases
     *      The enum cases to memoize.
     * @return Set&IsUnmodifiable
     *      The set of cases.
     * 
     * @throws \InvalidArgumentException
     *      If one enum case is of the wrong type.
     * 
     * @phpstan-param E ...$cases
     * @phpstan-return Set<E>&IsUnmodifiable
     */
    function memoize(\UnitEnum ...$cases): Set&IsUnmodifiable;

    /**
     * Gets the allowed enum class.
     * 
     * @return string
     *      The enum class name.
     * 
     * @phpstan-return class-string<E>
     */
    function getEnumClass(): string;
}
