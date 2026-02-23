<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Scalar;

use Time2Split\Help\Schema\Operator\AndSchema;

/**
 * Validate an integer element.
 * 
 * @package time2help\schema\scalar
 */
class IntSchema
extends AndSchema
{
    #[\Override]
    public function validateElement($element): bool
    {
        if (!\is_int($element))
            return false;

        return parent::validateElement($element);
    }

    /**
     * Whether the integer is identical to another one.
     * 
     * (Uses the `===` operator.)
     * 
     * @param int $integer
     *      The integer to be compared to.
     * @param int ...$orInteger
     *      More integers to be compared to.
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function is(int $integer, int ...$orInteger): static
    {
        return parent::sameAs($integer, ...$orInteger);
    }

    /**
     * Whether the integer is in an inclusive range.
     * 
     * (`$min <= $integer <= $max`)
     * 
     * @param int $min
     *      The lower bound of the range.
     * @param int $max
     *      The upper bound of the range.
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function between(int $min, int $max): static
    {
        return $this->buildSchemaFromClosure(
            fn(int $i) => $min <= $i && $i <= $max
        );
    }

    /**
     * Whether the integer is greater than a lower bound.
     * 
     * (`$min <= $integer`)
     * 
     * @param int $min
     *      The lower bound of the range.
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function min(int $min): static
    {
        return $this->buildSchemaFromClosure(
            fn(int $i) => $min <= $i
        );
    }

    /**
     * Whether the integer is lower than an upper bound.
     * 
     * (`$integer <= $max`)
     * 
     * @param int $max
     *      The upper bound of the range.
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function max(int $max): static
    {
        return $this->buildSchemaFromClosure(
            fn(int $i) =>  $i <= $max
        );
    }

    /**
     * Whether the integer is positive.
     * 
     * @param bool $strict
     *      - `true`:  the integer is `> 0`,
     *      - `false`: the integer is ` >= 0`.
     * @param bool $yes
     *      - `true`:  the integer is positive,
     *      - `false`: the integer is negative.
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function isPositive(bool $strict = true, bool $yes = true): static
    {
        if ($yes)
            return $this->buildSchemaFromClosure(
                $strict
                    ? fn(int $i) =>  $i > 0
                    : fn(int $i) =>  $i >= 0
            );

        return $this->isNegative(!$strict);
    }

    /**
     * Whether the integer is negative.
     * 
     * @param bool $strict
     *      - `true`:  the integer is `< 0`,
     *      - `false`: the integer is ` <= 0`.
     * @param bool $yes
     *      - `true`:  the integer is negative,
     *      - `false`: the integer is positive.
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function isNegative(bool $strict = true, bool $yes = true): static
    {
        if ($yes)
            return $this->buildSchemaFromClosure(
                $strict
                    ? fn(int $i) =>  $i < 0
                    : fn(int $i) =>  $i <= 0
            );
        return $this->isPositive(!$strict);
    }
}
