<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\Class\OfElements;
use Time2Split\Help\TriState;

/**
 * An unmodifiable sequence of edges.
 * 
 * (Template`<T>`)
 * 
 * @see PathEdge
 * @see Paths
 * 
 * @package time2help\container\path
 * @author Olivier Rodriguez (zuri)
 * 
 * @template T
 * @extends ContainerAA<int,PathEdge<T>>
 * @extends OfElements<T>
 */
interface Path extends
    ContainerAA,
    OfElements,
    IsUnmodifiable
{
    /**
     * Resolves the `Previous` and `Current` edges.
     * 
     * For each edge if it has the type:
     * - {@see PathEdgeType::Current} then the edge is removed.
     * - {@see PathEdgeType::Previous} then if it's not the first edge:
     *   the previous and the current edge are removed.
     * 
     * @phpstan-return Path<T>
     * 
     * @return Path
     *      The canonicalize path.
     */
    function canonical(): Path;

    /**
     * Whether the path is in its canonical form.
     * 
     * @return bool
     *  - `true` if no {@see PathEdgeType::Current} or  {@see PathEdgeType::Previous}
     *    edge exists,
     *  - `false`otherwise.
     */
    public function isCanonical(): bool;

    /**
     * Whether the first edge is a root.
     * 
     * @return TriState
     *  - `Yes` if the first edge must be rooted.
     *  - `No` if the first edge must not be rooted
     *  - `Maybe` if the first edge may be rooted (or not).
     */
    public function isRooted(): TriState;

    /**
     * Whether the last edge is a leaf.
     * 
     * @return TriState
     *  - `Yes` if the first edge must be rooted.
     *  - `No` if the first edge must not be rooted
     *  - `Maybe` if the first edge may be rooted (or not).
     */
    public function isLeafed(): TriState;

    // ========================================================================

    /**
     * Gets the labels of the path.
     */
    #[\Override]
    public function toListOfElements(): array;
}
