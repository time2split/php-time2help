<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\_internal;

use Time2Split\Help\Cast\Cast;
use Time2Split\Help\Container\Path;
use Time2Split\Help\Container\PathEdge;
use Time2Split\Help\Container\PathEdgeType;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\ContainerWithArrayStorage;
use Time2Split\Help\Container\Trait\CountableWithStorage;
use Time2Split\Help\Container\Trait\ElementsToListOfElements;
use Time2Split\Help\Container\Trait\IteratorAggregateWithArrayStorage;
use Time2Split\Help\Container\Trait\UnmodifiableContainerAA;
use Time2Split\Help\Container\Trait\UnmodifiableElementsUpdating;
use Time2Split\Help\Iterables;
use Time2Split\Help\TriState;

/**
 * @author Olivier Rodriguez (zuri)
 * 
 * @template T
 * @implements Path<T>
 * @implements \IteratorAggregate<int,PathEdge<T>>
 */
class PathImpl
implements
    Path,
    \IteratorAggregate
{
    /**
     * @use ArrayAccessWithStorage<int,PathEdge<T>>
     * @use IteratorAggregateWithArrayStorage<int,T>
     * @use ContainerWithArrayStorage<int,T>
     * @use ArrayAccessWithStorage<int,T>
     * @use ElementsToListOfElements<T>
     */
    use
        CountableWithStorage,
        IteratorAggregateWithArrayStorage,
        ContainerWithArrayStorage,
        ArrayAccessWithStorage,
        ElementsToListOfElements,
        UnmodifiableContainerAA,
        UnmodifiableElementsUpdating {
        UnmodifiableContainerAA::clear insteadof ContainerWithArrayStorage;
        UnmodifiableContainerAA::offsetSet insteadof ArrayAccessWithStorage;
        UnmodifiableContainerAA::offsetUnset insteadof ArrayAccessWithStorage;
    }

    /**
     * @var list<PathEdge<T>>
     */
    protected array $storage;

    protected bool $isCanonical;

    protected TriState $isRooted;

    protected TriState $isLeafed;

    /**
     * @param PathEdge<T> ... $edges
     */
    public function __construct(
        TriState $rooted,
        TriState $leafed,
        PathEdge ...$edges,
    ) {
        $this->storage = $edges;
        $this->isRooted = $rooted;
        $this->isLeafed = $leafed;
    }

    #[\Override]
    public function elements(): \Traversable
    {
        return Cast::iterableToIterator(
            Iterables::mapValue($this->storage, fn(PathEdge $edge) => $edge->getLabel())
        );
    }

    #[\Override]
    public function isCanonical(): bool
    {
        if (isset($this->isCanonical))
            return $this->isCanonical;

        foreach ($this as $pathEdge) {

            if (
                $pathEdge[PathEdgeType::Current] ||
                $pathEdge[PathEdgeType::Previous]
            )
                return $this->isCanonical = false;
        }
        return $this->isCanonical = true;
    }

    #[\Override]
    public function isRooted(): TriState
    {
        return $this->isRooted;
    }

    #[\Override]
    public function isLeafed(): TriState
    {
        return $this->isLeafed;
    }

    // ========================================================================

    #[\Override]
    public function canonical(): Path
    {
        $edges = [];

        foreach ($this as $edge) {
            $edgeType = $edge->getType();

            if ($edgeType[PathEdgeType::Current]);
            elseif ($edgeType[PathEdgeType::Previous]) {

                if (!empty($edges))
                    \array_pop($edges);
            } else {
                $edges[] = $edge;
            }
        }
        return new self($this->isRooted, $this->isLeafed, ...$edges);
    }
}
