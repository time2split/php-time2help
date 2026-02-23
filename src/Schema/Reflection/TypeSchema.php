<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Reflection;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Time2Split\Help\Reflections;
use Time2Split\Help\Schema\Operator\AndSchema;
use Time2Split\Help\Schema\Scalar\StringSchema;

/**
 * Validate a reflection type element.
 * 
 * @package time2help\schema\reflection
 */
final class TypeSchema
extends AndSchema
{
    #[\Override]
    public final function validateElement($element): bool
    {
        if (!$element instanceof ReflectionType)
            return false;

        return parent::validateElement($element);
    }

    // ========================================================================
    // BOOL

    public final function allowsNull(bool $yes = true): self
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionType $param) => $param->allowsNull() === true
                : fn(ReflectionType $param) => $param->allowsNull() === false
        );
        return $this;
    }


    // ========================================================================

    /**
     * Whether the type is an union type.
     * 
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function isUnionType(bool $yes = true): static
    {
        return $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionType $type) => $type instanceof ReflectionUnionType
                : fn(ReflectionType $type) => !$type instanceof ReflectionUnionType
        );
    }

    /**
     * Whether the type is an intersection type.
     * 
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function isIntersectionType(bool $yes = true): static
    {
        return $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionType $type) => $type instanceof ReflectionIntersectionType
                : fn(ReflectionType $type) => !$type instanceof ReflectionIntersectionType
        );
    }

    /**
     * Whether the type is a named type.
     * 
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function isNamedType(bool $yes = true): static
    {
        return $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionType $type) => $type instanceof ReflectionNamedType
                : fn(ReflectionType $type) => !$type instanceof ReflectionNamedType
        );
    }

    /**
     * Whether the type is a union/intersection type.
     * 
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function isComplexType(bool $yes = true): static
    {
        return $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionType $type) => $type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType
                : fn(ReflectionType $type) => !($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType)
        );
    }

    /**
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function isOfNamedType(string $name, string ...$moreNames): static
    {
        $names = [$name, ...$moreNames];

        return $this->buildSchemaFromClosure(
            fn(ReflectionType $type) => Reflections::isOfNamedTypes($type, $names)
        );
    }

    /**
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function hasAllNamedTypes(string $name, string ...$moreNames): static
    {
        $names = [$name, ...$moreNames];

        return $this->buildSchemaFromClosure(
            fn(ReflectionType $type) => Reflections::hasAllNamedType($type, $names)
        );
    }

    // ========================================================================
    // BUILDER

    public final function name(): StringSchema
    {
        return $this->buildSchema(new StringSchema(fn(ReflectionType $type) => (string)$type));
    }
}
