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
     * @param iterable<mixed,TriState|PathEdgeType|PathEdge|mixed> $labelOrEdgeOrTypeIt
     *      Every value will generate a {@see PathEdge} for the resulting path.
     * 
     *      If the first value is a {@see TriState} then it sets the
     *      {@see Path::isRooted()} value.
     * 
     *      If the last value is a {@see TriState} then it sets the
     *      {@see Path::isLeafed()} value.
     * 
     *      Otherwise, according to the type:
     * 
     *       - {@see PathEdge} brings directly the edge as the generated
     *         path edge.
     *       - `mixed` will generate an edge labelled by the value.
     * 
     * @param string|\Closure $classOrConstructor
     *      The constructor/class to create the path instance.
     * 
     *      - `$classOrConstructor(TriState $rooted, TriState $leafed, iterable<PathEdge> $edges):Path`
     * 
     * @return Path
     *      The path.
     * 
     * @template T
     * @phpstan-param iterable<TriState|T|PathEdgeType|PathEdge<T>> $labelOrEdgeOrTypeIt
     * @phpstan-param class-string<Path<T>>|\Closure(TriState,TriState,iterable<PathEdge<T>>):Path<T> $classOrConstructor
     * @phpstan-return Path<T>
     */
    public static function of(
        iterable $labelOrEdgeOrTypeIt,
        string|\Closure $classOrConstructor = PathImpl::class,
    ): Path {
        $edges =  PathEdges::listOf(
            $labelOrEdgeOrTypeIt,
            $rooted,
            $leafed
        );

        if (\is_string($classOrConstructor))
            return new $classOrConstructor($rooted, $leafed, $edges);
        else
            return $classOrConstructor($rooted, $leafed, $edges);
    }
}
