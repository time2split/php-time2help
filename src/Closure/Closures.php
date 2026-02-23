<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure;

use Closure;
use Time2Split\Help\Classes\NotInstanciable;

final class Closures
{
    use NotInstanciable;

    public static function or(Closure ...$functions): Closure
    {
        return function (mixed ...$args) use ($functions): bool {

            foreach ($functions as $f) {

                if ($f(...$args))
                    return true;
            }
            return false;
        };
    }

    public static function and(Closure ...$functions): Closure
    {
        return function (mixed ...$args) use ($functions): bool {

            foreach ($functions as $f) {

                if (!$f(...$args))
                    return false;
            }
            return true;
        };
    }

    /**
     * @param array<mixed> $values
     */
    public static function anyPredicate(array $values, Closure $predicate): Closure
    {
        return function (mixed ...$args) use ($values, $predicate) {

            foreach ($values as $v) {

                if ($predicate($v, ...$args))
                    return true;
            }
            return false;
        };
    }

    /**
     * @param array<mixed> $values
     */
    public static function allPredicate(array $values, Closure $predicate): Closure
    {
        return function (mixed ...$args) use ($values, $predicate) {

            foreach ($values as $v) {

                if (!$predicate($v, ...$args))
                    return false;
            }
            return true;
        };
    }
}
