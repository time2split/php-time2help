<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema;

use Time2Split\Help\Schema\Class\IsUnmodifiable;

/**
 * A schema composed of an internal list of child schemas.
 * 
 * @package time2help\schema
 */
interface OfSchemas
{
    /**
     * Adds a child schema.
     * 
     * The child schema must be cloned
     * 
     * @param Schema $schema
     *      The child schema to add.
     *      The schema reference is not preserved (a cloning is done)
     *      to avoid any external modification of the child schema.
     * 
     *      Note that even if the child schema is a {@see OfSchemas} instance
     *      it will never be marked as the last child schema (see {@see OfSchemas::and()}).
     * @return static
     *      This schema.
     * 
     * @phpstan-return $this
     */
    function schema(Schema $schema): static;

    // ========================================================================

    /**
     * Gets the last child schema to continue its definition.
     * 
     * If the last added child schema is not a {@see OfSchemas} instance,
     * then `$this` is returned.
     * 
     * @return Schema&OfSchemas
     *      The last created child schema,
     *      or `$this` if there is no parent.
     */
    function and(): Schema&OfSchemas;

    /**
     * Gets the parent schema.
     * 
     * @return Schema&OfSchemas
     *      The parent schema,
     *      or `$this` if there is no parent.
     */
    function up(): Schema&OfSchemas;

    /**
     * Wraps the root schema to be unmodifiable.
     * 
     * (This method may be called when the building process is done.)
     * 
     * @return Schema&IsUnmodifiable
     *      The unmodifiable root schema.
     */
    function commit(): Schema;

    // ========================================================================

    /**
     * Gets the inverse validation of the schema.
     * 
     * @return Schema&OfSchemas
     *      The new child schema.
     */
    function not(): Schema&OfSchemas;

    /**
     * Gets a new int child schema.
     * 
     * @param bool $castToInt
     *      - `true`:  The element to validate is casted to an integer before its validation.
     *      - `false`: The element to validate must be an integer before its validation.
     * @return IntSchema
     *      The new child schema.
     */
    function int(bool $castToInt = false): IntSchema;

    /**
     * Gets a new int string child schema.
     * 
     * @param bool $castToString
     *      - `true`:  The element to validate is casted into an string before its validation.
     *      - `false`: The element to validate must be an string before its validation.
     * @return StringSchema
     *      The new child schema.
     */
    function string(bool $castToString = false): StringSchema;

    /**
     * Gets a new int object child schema.
     * 
     * @param bool $castToObject
     *      - `true`:  The element to validate is casted into an object before its validation.
     *      - `false`: The element to validate must be an object before its validation.
     * @return ObjectSchema
     *      The new child schema.
     */
    function object(bool $castToObject = false): ObjectSchema;

    // ========================================================================

    /**
     * Whether the element is set (i.e. not null).
     * 
     * @param bool $isset
     *      - `true`:  the value must be set
     *      - `false`: the value must not be set
     * @return Schema&OfSchemas
     *      Its parent schema,
     *      or `$this` if there is no parent.
     */
    function isset(bool $isset = true): Schema&OfSchemas;

    /**
     * Whether the value element is the same as another one.
     * 
     * @param mixed $value
     *      The $value to be compared to.
     * @param mixed...$orValue
     *      More values to be compared to.
     * @return Schema&OfSchemas
     *      Its parent schema,
     *      or `$this` if there is no parent.
     */
    function sameAs(mixed $value, mixed ...$orValue): Schema&OfSchemas;

    /**
     * Whether the type of the element corresponds to a specific type.
     * 
     * @param string $type
     *      The type of the value.
     * @param string ...$orType
     *      More types the value can be.
     * @return Schema&OfSchemas
     *      Its parent schema,
     *      or `$this` if there is no parent.
     */
    function isOfType(string $type, string ...$orType): Schema&OfSchemas;
}
