<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema;

use Time2Split\Help\Schema\Impl\AbstractSchemaOfSchema;

/**
 * Validates a string element.
 * 
 * @package time2help\schema
 */
class StringSchema extends AbstractSchemaOfSchema
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
     * @return Schema&OfSchemas
     *      Its parent schema,
     *      or `$this` if there is no parent.
     */
    final function is(string $string, string ...$orString): Schema&OfSchemas
    {
        return $this->sameAs($string, ...$orString);
    }

    /**
     * Whether the string is prefixed by another one.
     * 
     * @param string $prefix
     *      The prefix
     * @return Schema&OfSchemas
     *      Its parent schema,
     *      or `$this` if there is no parent.
     */
    public final function startsWith(string $prefix): Schema&OfSchemas
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
     * @return Schema&OfSchemas
     *      Its parent schema,
     *      or `$this` if there is no parent.
     */
    public final function endsWith(string $suffix): Schema&OfSchemas
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
     * @return Schema&OfSchemas
     *      Its parent schema,
     *      or `$this` if there is no parent.
     */
    public final function contains(string $string): Schema&OfSchemas
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
     * @return Schema&OfSchemas
     *      Its parent schema,
     *      or `$this` if there is no parent.
     */
    public final function pregMatch(string $pattern): Schema&OfSchemas
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
        return $this->buildSchema(new IntSchema($this, fn(string $string) => \strlen($string)));
    }
}
