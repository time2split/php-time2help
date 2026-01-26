<?php

declare(strict_types=1);

namespace Time2Split\Help\Memory;

use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Memory\_internal\EnumSetMemoizerBitIndexImpl;

/**
 * Factories and functions on memoizers.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\memory
 */
final class Memoizers
{
    use NotInstanciable;

    /**
     * Provides an EnumSetMemoizer.
     * 
     * @template E of \UnitEnum
     * 
     * @phpstan-param class-string<E>|E $enumClass
     * @phpstan-param null|E[][] $allowedCases
     * @phpstan-return EnumSetMemoizer<E> $allowedCases
     * 
     * @param string|\UnitEnum $enumClass
     *      The class for the enum cases.
     * @param null|\UnitEnum[][] $allowedCases
     *      The allowed combinations of enum cases.
     * @return EnumSetMemoizer<\UnitEnum> $allowedCases
     *      The memoizer of enum cases.
     */
    public static function ofEnum(string|\UnitEnum $enumClass, ?array $allowedCases = null): EnumSetMemoizer
    {
        if (!\is_a($enumClass, \UnitEnum::class, true))
            throw new \InvalidArgumentException("$enumClass must be a \UnitEnum subtype");

        if (!\is_string($enumClass))
            $enumClass = \get_class($enumClass);

        /*
        if (\extension_loaded('bcmath') && \version_compare(PHP_VERSION, '8.4.0', '>=')) {
            // TODO: use a bcmath number index implementation if
        }
        //*/
        return EnumSetMemoizerBitIndexImpl::create($enumClass, $allowedCases);
    }
}
