<?php

namespace Time2Split\Help;

use Closure;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use Time2Split\Help\Classes\NotInstanciable;

class Reflections
{
    use NotInstanciable;

    /**
     * Whether the list of named types of a type corresponds to a unordered list of names.
     * 
     * @param ReflectionType $type
     *      The $type.
     * @param array<string> $names
     *      The names of the types to find.
     * @return bool
     *      `true` if there is a bijection between the {@see ReflectionNamedType} names in `$type` and `$names`. 
     */
    public static function isOfNamedTypes(ReflectionType $type, array $names): bool
    {
        $namedTypes = self::getAllNamedTypes($type);
        $nb = \count($namedTypes);

        if ($nb !== \count($names))
            return false;

        $remains = self::findNamedTypes($namedTypes, $names);
        return $nb === \count($remains);
    }

    /**
     * Whether a type has specifics named types.
     * 
     * @param ReflectionType $type
     *      The $type.
     * @param array<string> $names
     *      The names of the types to find.
     * @return bool
     *      `true` if each name of `$name` is an existant {@see ReflectionNamedType} name in `$type`.
     */
    public static function hasAllNamedType(ReflectionType $type, array $names): bool
    {
        $namedTypes = self::getAllNamedTypes($type);
        $remains = self::findNamedTypes($namedTypes, $names);
        return \count($names) === \count($remains);
    }

    /**
     * Gets the number of named types of a type.
     * 
     * @return int
     *      The number of {@see ReflectionNamedType} in `$type`.
     */
    public static function getNumberOfNamedTypes(ReflectionType $type): int
    {
        $namedTypes = self::getAllNamedTypes($type);
        return \count($namedTypes);
    }

    /**
     * Gets the name of each named type of a type.
     * 
     * @param ReflectionType $type
     *      The $type.
     * @return string[]
     *      The names of each {@see ReflectionNamedType} in the type.
     */
    public static function getAllNamedTypes(ReflectionType $type): array
    {
        $types = [$type];
        $names = [];

        while (!empty($types)) {
            $type = \array_pop($types);

            if ($type instanceof ReflectionNamedType) {
                $names[] = $type->getName();
            } else {
                $types = \array_merge($types, $type->getTypes());
            }
        }
        return $names;
    }

    /**
     * @param array<string> $namedTypes
     * @param array<string> $names
     * @return string[]
     *      The founded names.
     */
    private static function findNamedTypes(array $namedTypes, array $names): array
    {
        $found = [];

        foreach ($names as $name) {
            $i = \array_search($name, $namedTypes);

            if ($i !== false) {
                $found[] = $name;
                unset($namedTypes[$i]);
            }
        }
        return $found;
    }

    // ========================================================================

    public static function closureToString(ReflectionFunction|callable $fn): string
    {
        if (!$fn instanceof ReflectionFunction)
            $fn = new ReflectionFunction($fn);

        $name = $fn->getName();
        $return = self::typeToString($fn->getReturnType());

        $parameters = [];

        foreach ($fn->getParameters() as $param)
            $parameters[] = self::parameterToString($param);

        $parameters = \implode(', ', $parameters);

        return "$name($parameters): $return";
    }

    public static function parameterToString(ReflectionParameter $param): string
    {
        $type = self::typeToString($param->getType());
        $var = $param->getName();
        $variadic = $param->isVariadic() ? '...' : '';
        return "$type $variadic\$$var";
    }

    public static function typeToString(?ReflectionType $type): string
    {
        if ($type === null)
            return '';
        return $type->__toString();
    }
}
