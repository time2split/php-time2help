<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Impl;

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
 * An implementation of a path.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\path
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

    protected ?bool $canonicalized;

    protected TriState $rooted;

    protected TriState $leafed;

    /**
     * @param iterable<PathEdge<T>> $edges
     */
    public function __construct(
        TriState $rooted,
        TriState $leafed,
        iterable $edges,
        ?bool $canonicalized = null,
    ) {
        $this->storage = \iterator_to_array($edges, false);
        $this->rooted = $rooted;
        $this->leafed = $leafed;
        $this->canonicalized = $canonicalized;
    }

    #[\Override]
    public function elements(): \Traversable
    {
        return Cast::iterableToIterator(
            Iterables::mapValue($this->storage, fn(PathEdge $edge) => $edge->getLabel())
        );
    }

    protected function canonicalized(): bool
    {
        if (isset($this->canonicalized))
            return $this->canonicalized;

        return $this->canonicalized = $this->isCanonical();
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
            ) {
                assert(
                    $pathEdge[PathEdgeType::Current] xor
                        $pathEdge[PathEdgeType::Previous]
                );
                return $this->isCanonical = false;
            }
        }
        return $this->isCanonical = true;
    }

    #[\Override]
    public function rooted(): TriState
    {
        return $this->rooted;
    }

    #[\Override]
    public function leafed(): TriState
    {
        return $this->leafed;
    }

    // ========================================================================

    #[\Override]
    public function canonical(): static
    {
        if ($this->count() === 0 || $this->canonicalized())
            return $this;

        $edges = [];

        foreach ($this as $edge) {
            $edgeType = $edge->getType();

            if ($edgeType[PathEdgeType::Current]);
            elseif ($edgeType[PathEdgeType::Previous]) {

                if (\count($edges) > 0)
                    \array_pop($edges);
                else
                    $edges[] = $edge;
            } else {
                $edges[] = $edge;
            }
        }
        $last = $edge;

        if ($last[PathEdgeType::Current] || $last[PathEdgeType::Previous])
            $leafed = TriState::No;
        else
            $leafed = $this->leafed;

        return new static(
            $this->rooted,
            $leafed,
            $edges,
            canonicalized: true
        );
    }
}
