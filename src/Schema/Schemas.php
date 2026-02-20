<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema;

use Closure;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Functions;
use Time2Split\Help\Schema\Class\IsUnmodifiable;
use Time2Split\Help\Schema\Impl\AbstractSchemaOfSchema;
use Time2Split\Help\Schema\Impl\NotSchema;
use Time2Split\Help\Schema\Reflection\ClassSchema;
use Time2Split\Help\Schema\Reflection\ParameterSchema;
use Time2Split\Help\Schema\Reflection\ClosureSchema;
use Time2Split\Help\Schema\Reflection\TypeSchema;

/**
 * Factories on schemas.
 * 
 * @package time2help\schema
 */
final class Schemas
{
    use NotInstanciable;

    /**
     * Gets a schema.
     */
    public static function schema(): Schema&OfSchemas
    {
        return new class() extends AbstractSchemaOfSchema {};
    }

    /**
     * Gets a schema that valide the negation of its childs.
     */
    public static function not(): Schema&OfSchemas
    {
        return new NotSchema();
    }

    /**
     * Gets a class schema
     */
    public static function class(): ClassSchema
    {
        return new ClassSchema();
    }

    /**
     * Gets an object schema
     */
    public static function object(bool $castToObject = false): ObjectSchema
    {
        return new ObjectSchema(transformElement: $castToObject ? Functions::castToObject(...) : null);
    }

    /**
     * Gets a closure schema
     */
    public static function closure(): ClosureSchema
    {
        return new ClosureSchema;
    }

    /**
     * Gets a parameter schema
     */
    public static function parameter(): ParameterSchema
    {
        return new ParameterSchema();
    }

    /**
     * Gets a type shema
     */
    public static function type(): TypeSchema
    {
        return new TypeSchema();
    }

    /**
     * Gets a string schema
     */
    public static function string(bool $castToString = false): StringSchema
    {
        return new StringSchema(transformElement: $castToString ? Functions::castToString(...) : null);
    }

    /**
     * Gets an int shcema
     */
    public static function int(bool $castToInt = false): IntSchema
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
