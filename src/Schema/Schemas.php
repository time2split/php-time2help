<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema;

use Closure;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Functions;
use Time2Split\Help\Schema\Class\IsUnmodifiable;
use Time2Split\Help\Schema\Operator\AndSchema;
use Time2Split\Help\Schema\Operator\NotSchema;
use Time2Split\Help\Schema\Operator\OrSchema;
use Time2Split\Help\Schema\Reflection\ClassSchema;
use Time2Split\Help\Schema\Reflection\ParameterSchema;
use Time2Split\Help\Schema\Reflection\ClosureSchema;
use Time2Split\Help\Schema\Reflection\TypeSchema;
use Time2Split\Help\Schema\Scalar\IntSchema;
use Time2Split\Help\Schema\Scalar\ObjectSchema;
use Time2Split\Help\Schema\Scalar\StringSchema;

/**
 * Factories on schemas.
 * 
 * @package time2help\schema
 */
final class Schemas
{
    use NotInstanciable;

    /**
     * Gets a schema that validates the intersection of its childs.
     * 
     * ```php
     * $child[0] && $child[1] && ... && $child[$last]
     * ```
     */
    public static function schema(Schema ...$andSchemas): AndSchema
    {
        return new AndSchema(childs: $andSchemas);
    }

    /**
     * Gets a schema that validates the union of its childs. 
     * 
     * ```php
     * $child[0] || $child[1] || ... || $child[$last]
     * ```
     */
    public static function union(Schema ...$orSchemas): OrSchema
    {
        return new OrSchema(childs: $orSchemas);
    }

    /**
     * Gets a schema that validates the negation of the intersection of its childs.
     * 
     * ```php
     * !($child[0] && $child[1] && ...  && $child[$last])
     * ```
     */
    public static function negation(Schema ...$notSchemas): NotSchema
    {
        return new NotSchema(childs: $notSchemas);
    }

    // ========================================================================

    /**
     * Gets a class schema.
     */
    public static function class(): ClassSchema
    {
        return new ClassSchema();
    }

    /**
     * Gets an object schema.
     */
    public static function object(bool $castToObject = false): ObjectSchema
    {
        return new ObjectSchema(transformElement: $castToObject ? Functions::castToObject(...) : null);
    }

    /**
     * Gets a closure schema.
     */
    public static function closure(): ClosureSchema
    {
        return new ClosureSchema;
    }

    /**
     * Gets a parameter schema.
     */
    public static function parameter(): ParameterSchema
    {
        return new ParameterSchema();
    }

    /**
     * Gets a type shema.
     */
    public static function type(): TypeSchema
    {
        return new TypeSchema();
    }

    /**
     * Gets a string schema.
     */
    public static function string(bool $castToString = false): StringSchema
    {
        return new StringSchema(transformElement: $castToString ? Functions::castToString(...) : null);
    }

    /**
     * Gets an integer schema.
     */
    public static function integer(bool $castToInt = false): IntSchema
    {
        return new IntSchema(transformElement: $castToInt ? Functions::castToInt(...) : null);
    }

    // ========================================================================

    /**
     * Gets an unmodifiable schema from a closure.
     * 
     * @param Closure(mixed):bool $validate
     *      Validate an element.
     * 
     *      - $validate(mixed $element):bool
     */
    public static function fromClosure(Closure $validate): Schema&IsUnmodifiable
    {
        return new class($validate) implements Schema, IsUnmodifiable {

            public function __construct(
                private Closure $validate,
            ) {}

            public function validate(mixed $element): bool
            {
                return ($this->validate)($element);
            }
        };
    }
}
