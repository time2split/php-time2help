<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\Impl\PathImpl;
use Time2Split\Help\TriState; // For doc

/**
 * Factories on paths.
 * 
 * @package time2help\container\path
 * @author Olivier Rodriguez (zuri)
 */
final class Paths
{
    use NotInstanciable;

    /**
     * Gets a Path.
     * 
     * @param iterable<*,mixed|PathEdgeType|PathEdge> $labelOrEdge
     *      Every value will generate a {@see PathEdge}.
     * 
     *      If the first value is a {@see TriState} then it sets the
     *      {@see Path::isRooted()} value.
     *      
     *      If the last value is a {@see TriState} then it sets the
     *      {@see Path::isLeafed()} value.
     *      
     *      Otherwise, according to the type:
     *       - `mixed` will generate an edge labelled by the value with a type
     *         corresponding to the argument `$middled`.
     *       - {@see PathEdge} brings directly the edge as the generated
     *         path edge.
     * @param string|\Closure $classOrConstructor
     *      The constructor/class to create the path instance.
     * @return Path
     *      The path.
     * 
     * @template T
     * @phpstan-param iterable<T|PathEdgeType|PathEdge<T>> $labelOrEdge
     * @phpstan-param class-string<Path<T>>|\Closure(TriState,TriState,PathEdge<T>[]):Path<T> $classOrConstructor
     * @phpstan-return Path<T>
     */
    public static function of(
        iterable $labelOrEdge,
        string|\Closure $classOrConstructor = PathImpl::class,
    ): Path {
        $edges =  PathEdges::listOf($labelOrEdge);
        $rooted = \array_shift($edges);
        $leafed = \array_pop($edges);

        if (\is_string($classOrConstructor))
            return new $classOrConstructor($rooted, $leafed, ...$edges);
        else
            return $classOrConstructor($rooted, $leafed, ...$edges);
    }
}
