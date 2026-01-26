<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Class\IsUnmodifiable;

/**
 * An edge of a path.
 * 
 * (Template`<T>`)
 * 
 * An edge can be accessed  like an array to refer to its type:
 * 
 * - `$edge[PathEdgeType::Leaf]` is equivalent to
 * - `$edge->getType()[PathEdgeType::Leaf]`.
 * 
 * and
 * 
 * - `$edge->count()` is equivalent to
 * - `$edge->getType()->count()`.
 * 
 * @see Path
 * @see PathEdgeType
 * 
 * @package time2help\container\path
 * @author Olivier Rodriguez (zuri)
 * 
 * @template T
 * @extends \ArrayAccess<PathEdgeType,bool>
 */
interface PathEdge
extends
    \ArrayAccess,
    \Countable,
    IsUnmodifiable
{
    /**
     * Gets the edge type.
     * 
     * @phpstan-return Set<PathEdgeType>
     * 
     * @return Set (of {@see PathEdgeType})
     *      The type of the edge.
     */
    public function getType(): Set;

    /**
     * Gets the edge label.
     * 
     * @phpstan-return T
     */
    public function getLabel(): mixed;
}
