<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure;

use Closure;
use ReflectionFunction;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Optional;

/**
 * Functions to inject parameters value on closure.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\closure
 */
final class Captures
{
    use NotInstanciable;

    // ========================================================================

    /**
     * Gets a closure to valid a parameter's position.
     * 
     * @param int $pos
     *      The position of the parameter into a function's list of parameters.
     * @return Closure(\ReflectionParameter):bool
     *      A closure able to check if a parameter is at a specific position.
     */
    public static function validParameterPositionClosure(int $pos): Closure
    {
        return function (\ReflectionParameter $p) use ($pos): bool {
            return $p->getPosition() === $pos;
        };
    }

    /**
     * Gets a closure to valid a parameter's name.
     * 
     * @param string $name
     *      The name of the parameter to valid.
     * @return Closure(\ReflectionParameter):bool
     *      A closure able to check if a parameter has a specific name.
     */
    public static function validParameterNameClosure(string $name): Closure
    {
        return function (\ReflectionParameter $p) use ($name): bool {
            return $p->getName() === $name;
        };
    }

    /**
     * Gets a closure to valid a parameter's type.
     * 
     * @param string $typeName
     *      The name of the type to valid.
     * @return Closure(\ReflectionParameter):bool
     *      A closure able to check if a parameter has a specific type.
     * 
     *      It can handle the complex types 
     *      {@see \ReflectionUnionType} and
     *      {@see \ReflectionIntersectionType}.
     *      In this case the parameter type is valid if one of its subtypes correspond to `$typeName`.
     */
    public static function validParameterTypeClosure(string $typeName): Closure
    {
        return function (\ReflectionParameter $p) use ($typeName): bool {

            if (!$p->hasType())
                return false;

            $type = $p->getType();

            if ($type instanceof \ReflectionNamedType)
                return Captures::validNamedType($typeName, $type);
            else
                /** @var \ReflectionUnionType|\ReflectionIntersectionType $type */
                return Captures::validComplexType($typeName, $type);
        };
    }

    /**
     * @internal
     */
    public static function validNamedType(string $typeName, \ReflectionNamedType $type): bool
    {
        return $type->getName() === $typeName;
    }

    /**
     * @internal
     */
    public static function validComplexType(string $typeName, \ReflectionUnionType|\ReflectionIntersectionType $types): bool
    {
        foreach ($types->getTypes() as $type) {

            if ($type instanceof \ReflectionNamedType) {

                if (Captures::validNamedType($typeName, $type))
                    return true;
            } else {

                /** @var \ReflectionUnionType|\ReflectionIntersectionType $type */
                if (Captures::validComplexType($typeName, $type))
                    return true;
            }
        }
        return false;
    }

    // ========================================================================

    /**
     * Searches the parameters of a function to be injected.
     * 
     * @param Closure $theFunction
     *      The closure in which to inject parameters.
     * @param iterable<mixed,ParameterInjection> $injections
     *      The parameters to inject into the function.
     * @return array<int,CapturedParameter>
     *      The parameters of the function in which to inject a value.
     * 
     *      If no parameter is found for an injection then the array is empty.
     */
    public static function captureParameters(
        Closure $theFunction,
        iterable $injections
    ): array {
        $ret = [];
        $reflect = new \ReflectionFunction($theFunction);

        foreach ($injections as $injection) {
            $point = self::searchOneArgumentInsertionPoint(
                $reflect,
                $injection
            );
            if ($point === null)
                return [];

            $ret[] = $point;
        }
        return $ret;
    }

    private static function searchOneArgumentInsertionPoint(
        \ReflectionFunction $reflect,
        ParameterInjection $inject,
    ): ?CapturedParameter {
        $params = $reflect->getParameters();
        $isValidParam = $inject->isValidParameter;

        foreach ($params as $p) {

            if ($isValidParam($p))
                return $inject->capture($p);
        }
        return null;
    }


    private static function comparePointsPosition(
        CapturedParameter $a,
        CapturedParameter $b,
    ): int {
        return $a->parameter->getPosition() - $b->parameter->getPosition();
    }

    /**
     * Gets a closure from a function proceeding an injection of some parameters.
     * 
     * Calling the obtained closure calls the underlying base function with the injected
     * parameters set properly in the list of its arguments.
     * The injected parameters are not a part of the closure's parameter list.
     * 
     * For instance:
     * ```
     * // We want to inject $data into the "$array" parameter of:
     * // array_map(?callable $callback, array $array, array ...$arrays)
     * $data = [
     *     'a' => 10,
     *     'b' => 20,
     *     'c' => 30
     * ];
     * $map = Captures::Captures(
     *     \array_map(...),
     *     [new ParameterInjection(
     *         Captures::validParameterNameClosure('array'),
     *         $data
     *     )]
     * );
     * // Gets the `Optional` value
     * assert($map->isPresent());
     * $map = $map->get();
     * 
     * print_r($map(fn($v) => $v / 10));
     * print_r($map(fn($v) => $v * 2));
     * ########
     * Displays
     * ########
     * Array
     * (
     *     [a] => 1
     *     [b] => 2
     *     [c] => 3
     * )
     * Array
     * (
     *     [a] => 20
     *     [b] => 40
     *     [c] => 60
     * )
     * ```
     * 
     * @phpstan-return Optional<Closure(mixed ...$args):mixed>
     * 
     * @param Closure $theFunction
     *      The function in which to inject parameters.
     * 
     * @param ParameterInjection|iterable<mixed,ParameterInjection> $injections
     *      The parameters to inject into the function.
     * 
     * @return Optional (of {@see Closure})
     * 
     *      A closure corresponding to the initial function where the 
     *      injected parameters have been removed from the parameter list.
     */
    public static function inject(
        Closure $theFunction,
        iterable|ParameterInjection $injections,
    ): Optional {

        if ($injections instanceof ParameterInjection)
            $injections = [$injections];

        $captures = self::captureParameters($theFunction, $injections);

        if (empty($captures))
            return Optional::empty();

        \usort($captures, self::comparePointsPosition(...));

        $fn = function (mixed ...$args) use ($theFunction, $captures): mixed {
            $nextCapture = $captures[0];
            $argsCopied =
                $nextPosition =
                $length =
                $nextCapture->parameter->getPosition();
            $nextPosition = $argsCopied;
            // Copy the arguments before the first capture
            $effective = \array_slice($args, 0, $length);

            do {
                \array_shift($captures);
                $capture = $nextCapture;
                $position = $nextPosition;
                $isRef = $capture->parameter->isPassedByReference();

                if ($isRef)
                    $send = &$capture->value;
                else
                    $send = $capture->value;

                $effective[] = &$send;
                unset($send);
                $argsOffset = $argsCopied;

                if (empty($captures))
                    $length = null;
                else {
                    $nextCapture = $captures[0];
                    $nextPosition = $nextCapture->parameter->getPosition();
                    $length = $nextPosition - $position - 1;
                    $argsCopied += $length;
                }
                // Copy the arguments before the next capture
                $selection = \array_slice($args, $argsOffset, $length);
                $effective = [...$effective, ...$selection];
            } while (!empty($captures));

            $reflect = new ReflectionFunction($theFunction);

            if ($reflect->hasReturnType())
                return $theFunction(...$effective);

            $theFunction(...$effective);
            return null;
        };
        return Optional::of($fn);
    }
}
