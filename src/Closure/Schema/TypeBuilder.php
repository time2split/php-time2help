<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

final class TypeBuilder
extends StringBuilder
{
    public final function allowsNull(bool $allowsNull = true): Schema&OfSchemas
    {
        return $this->buildCheckClosure(
            $allowsNull
                ? fn(ReflectionType $type) => $type->allowsNull() === true
                : fn(ReflectionType $type) => $type->allowsNull() === false
        );
    }

    public final function isUnionType(bool $isUnion = true): Schema&OfSchemas
    {
        return $this->buildCheckClosure(
            $isUnion
                ? fn(ReflectionType $type) => $type instanceof ReflectionUnionType
                : fn(ReflectionType $type) => !$type instanceof ReflectionUnionType
        );
    }

    public final function isIntersectionType(bool $isIntersection = true): Schema&OfSchemas
    {
        return $this->buildCheckClosure(
            $isIntersection
                ? fn(ReflectionType $type) => $type instanceof ReflectionIntersectionType
                : fn(ReflectionType $type) => !$type instanceof ReflectionIntersectionType
        );
    }

    public final function isNamedType(bool $isNamed = true): Schema&OfSchemas
    {
        return $this->buildCheckClosure(
            $isNamed
                ? fn(ReflectionType $type) => $type instanceof ReflectionNamedType
                : fn(ReflectionType $type) => !$type instanceof ReflectionNamedType
        );
    }

    public final function hasNamedType(string $name, string ...$moreNames): Schema&OfSchemas
    {
        $names = [$name, ...$moreNames];

        return $this->buildCheckClosure(
            fn(ReflectionType $type) => self::_hasType($type, $names)
        );
    }

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

    private static function _hasTypeName(ReflectionNamedType $type, array $names): false|int
    {
        return \array_search($type->getName(), $names);
    }


    // ========================================================================


    public final function unionHaveAll(array $typeSchemas): Schema&OfSchemas
    {
        return $this->_unionHaveAll(...$typeSchemas);
    }

    private function _unionHaveAll(TypeSchema ...$schemas): Schema&OfSchemas {}
}
