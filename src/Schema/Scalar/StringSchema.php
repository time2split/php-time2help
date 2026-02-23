<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Scalar;

use Time2Split\Help\Schema\Operator\AndSchema;

/**
 * Validates a string element.
 * 
 * @package time2help\schema\scalar
 */
class StringSchema extends AndSchema
{
    #[\Override]
    public function validateElement(mixed $element): bool
    {
        if (!\is_string($element))
            return false;

        return parent::validateElement($element);
    }

    // ========================================================================
    // CHECK

    /**
     * Whether the string is identical to another one.
     * 
     * (Uses the `===` operator.)
     * 
     * @param string $string
     *      The string to be compared to.
     * @param string...$orString
     *      More strings to be compared to.
     * 
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function is(string $string, string ...$orString): static
    {
        return $this->sameAs($string, ...$orString);
    }

    /**
     * Whether the string is prefixed by another one.
     * 
     * @param string $prefix
     *      The prefix
     * 
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function startsWith(string $prefix): static
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => \str_starts_with((string)$funValue, $prefix),
        );
    }

    /**
     * Whether the string is suffixed by another one.
     * 
     * @param string $suffix
     *      The suffix
     * 
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function endsWith(string $suffix): static
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => \str_ends_with((string)$funValue, $suffix),
        );
    }

    /**
     * Whether the string contains another one.
     * 
     * @param string $string
     *      The sub string
     * 
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function contains(string $string): static
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => \str_contains((string)$funValue, $string),
        );
    }

    /**
     * Whether the string is a match of a PCRE regular expression.
     * 
     * @param string $pattern
     *      The PCRE regular expression
     * 
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function pregMatch(string $pattern): static
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => 1 === \preg_match($pattern, (string)$funValue),
        );
    }

    // ========================================================================
    // SCHEMA

    /**
     * Gets an int schema on the element (string) length.
     * 
     * @return IntSchema
     *      The integer schema on the string length.
     */
    public final function strlen(): IntSchema
    {
        return $this->buildSchema(new IntSchema(transformElement: fn(string $string) => \strlen($string)));
    }
}
