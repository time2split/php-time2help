<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Classes\NotInstanciable;
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
     * (Template`<T>`)
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
     * 
     * @return Path
     *      The path.
     * 
     * @template T
     * @phpstan-param iterable<T|PathEdgeType|PathEdge<T>> $labelOrEdge
     * @phpstan-return Path<T>
     */
    public static function of(iterable $labelOrEdge): Path
    {
        return PathEdges::makePathOf($labelOrEdge);
    }
}
