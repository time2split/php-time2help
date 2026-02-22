<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema;

use Time2Split\Help\Schema\Operator\AndSchema; // for doc
use Time2Split\Help\Schema\Operator\NotSchema; // for doc
use Time2Split\Help\Schema\Operator\OrSchema; // for doc
use Time2Split\Help\Container\ContainerBase;
use Time2Split\Help\Schema\Scalar\IntSchema;
use Time2Split\Help\Schema\Scalar\ObjectSchema;
use Time2Split\Help\Schema\Scalar\StringSchema;

/**
 * A schema composed of an internal list of child schemas.
 * 
 * @package time2help\schema
 * 
 * @extends ContainerBase<int,Schema>
 */
interface OfSchemas extends ContainerBase
{
    /**
     * Gets a new integer child schema.
     * 
     * @param bool $castToInteger
     * The element to validate is casted to an integer before its validation.
     * 
     * @return IntSchema
     *      The new child schema.
     */
    function integer(bool $castToInteger = false): IntSchema;

    /**
     * Gets a new integer child schema.
     * 
     * The element to validate is casted to an integer before its validation.
     * 
     * @return IntSchema
     *      The new child schema.
     */
    function toInteger(): IntSchema;

    /**
     * Gets a new string child schema.
     * 
     * @param bool $castToString
     * The element to validate is casted to a string before its validation.
     * 
     * @return StringSchema
     *      The new child schema.
     */
    function string(bool $castToString = false): StringSchema;

    /**
     * Gets a new string child schema.
     * 
     * The element to validate is casted to an integer before its validation.
     * 
     * @return StringSchema
     *      The new child schema.
     */
    function toString(): StringSchema;

    /**
     * Gets a new object child schema.
     * 
     * @param bool $castToObject
     * The element to validate is casted to an object before its validation.
     * 
     * @return ObjectSchema
     *      The new child schema.
     */
    function object(bool $castToObject = false): ObjectSchema;

    /**
     * Gets a new object child schema.
     * 
     * The element to validate is casted to an integer before its validation.
     * 
     * @return ObjectSchema
     *      The new child schema.
     */
    function toObject(): ObjectSchema;

    // ========================================================================

    /**
     * Adds an `and` schema as a child.
     * 
     * The child schema is an {@see AndSchema}
     * containing all the arguments of this method.
     * 
     * @param Schema $schema
     *      The first child schema.
     * @param Schema $and
     *      The second child schema.
     * @param Schema $andMore
     *      More child schemas.
     * @return static `$this`
     * 
     * @phpstan-return $this
     */
    public function intersectionOf(Schema $schema, Schema $and, Schema ...$andMore): static;

    /**
     * Adds an `or` schema as a child.
     * 
     * The child schema is an {@see OrSchema}
     * containing all the arguments of this method.
     * 
     * @param Schema $schema
     *      The first child schema.
     * @param Schema $or
     *      The second child schema.
     * @param Schema $orMore
     *      More child schemas.
     * @return static `$this`
     * 
     * @phpstan-return $this
     */
    public function unionOf(Schema $schema, Schema $or, Schema ...$orMore): static;

    /**
     * Adds an `not` schema as a child.
     * 
     * The child schema is an {@see NotSchema}
     * containing all the arguments of this method.
     * 
     * @param Schema $schema
     *      The first child schema.
     * @param Schema $andMoreNot
     *      More child schemas.
     * @return static `$this`
     * 
     * @phpstan-return $this
     */
    public function negationOf(Schema $schema, Schema ...$andMoreNot): static;

    /**
     * Adds a schema as a child.
     * 
     * @param Schema $schema
     *      The child schema.
     * @return static `$this`
     * 
     * @phpstan-return $this
     */
    // public function subSchema(Schema $schema): static;

    // ========================================================================

    /**
     * Whether the element is set (i.e. not null).
     * 
     * @param bool $isset
     *      - `true`:  the element must be set
     *      - `false`: the element must not be set
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    function isset(bool $isset = true): static;

    /**
     * Whether the value element is the same as another one.
     * 
     * @param mixed $value
     *      The value to be compared to.
     * @param mixed...$orValue
     *      More values to be compared to.
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    function sameAs(mixed $value, mixed ...$orValue): static;

    /**
     * Whether the type of the element corresponds to a specific type.
     * 
     * @param string $type
     *      The type of the element.
     * @param string ...$orType
     *      More types the element can be.
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    function isOfType(string $type, string ...$orType): static;
}
