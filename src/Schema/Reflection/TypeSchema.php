<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Reflection;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
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
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function hasAllNamedType(string $name, string ...$moreNames): static
    {
        $names = [$name, ...$moreNames];

        return $this->buildSchemaFromClosure(
            fn(ReflectionType $type) => self::_hasType($type, $names)
        );
    }

    /**
     * @param array<string> $names
     */
    private static function _hasType(ReflectionType $type, array $names): bool
    {
        $names = \array_unique($names);
        $types = [$type];

        while (!empty($types) && !empty($names)) {
            $type = \array_pop($types);

            if ($type instanceof ReflectionNamedType) {
                $i = self::_hasTypeName($type, $names);

                if ($i !== false)
                    unset($names[$i]);
            } else {
                $types = \array_merge($types, $type->getTypes());
            }
        }
        return empty($names);
    }

    /**
     * @param array<string> $names
     */
    private static function _hasTypeName(ReflectionNamedType $type, array $names): false|int
    {
        return \array_search($type->getName(), $names);
    }

    // ========================================================================
    // BUILDER

    public final function name(): StringSchema
    {
        return $this->buildSchema(new StringSchema(fn(ReflectionType $type) => (string)$type));
    }
}
