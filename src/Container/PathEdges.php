<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Arrays;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\_internal\AbstractPathEdge;
use Time2Split\Help\Memory\Memoizers;
use Time2Split\Help\Optional;
use Time2Split\Help\TriState;

/**
 * Factories on path edges.
 * 
 * @package time2help\container\path
 * @author Olivier Rodriguez (zuri)
 */
final class PathEdges
{
    use NotInstanciable;

    /**
     * @internal
     * @phpstan-return Set<PathEdgeType>
     */
    private static function setOfEnum(PathEdgeType ...$cases): Set
    {
        static $memory = Memoizers::ofEnum(PathEdgeType::class);
        return $memory->memoize(...$cases);
    }

    /**
     * Gets the edge list from a list of labels/edges/types.
     * 
     * @param iterable<mixed,TriState|PathEdgeType|PathEdge|mixed> $labelOrEdgeOrTypeIt
     *      Every value will generate a {@see PathEdge} for the resulting path.
     * 
     *      If the first value is a {@see TriState} then it sets the
     *      `$rooted`` value.
     * 
     *      If the last value is a {@see TriState} then it sets the
     *      `$leafed` value.
     * 
     *      Otherwise, according to the type:
     * 
     *       - {@see PathEdge} brings directly the edge as the generated
     *         path edge.
     *       - `mixed` will generate an edge labelled by the value.
     * 
     * @param ?TriState $rooted (ouptut) The rooted value of the path.
     * @param ?TriState $leafed (ouptut) The leafed value of the path.
     * 
     * @return The list of edges.
     * 
     * @template T
     * @phpstan-param iterable<T|PathEdgeType|PathEdge<T>> $labelOrEdgeOrTypeIt
     * @phpstan-return PathEdge<T>[]
     * 
     * @phpstan-ignore parameterByRef.unusedType, parameterByRef.unusedType
     * 
     */
    public static function listOf(
        iterable $labelOrEdgeOrTypeIt,
        ?TriState &$rooted,
        ?TriState &$leafed
    ): array {
        $labels = \iterator_to_array($labelOrEdgeOrTypeIt);

        $first = Arrays::firstValue($labels);
        $hasRootedValue = $first instanceof TriState;

        if ($hasRootedValue) {
            \array_shift($labels);
            $rooted = $first;
        } else
            $rooted = TriState::Maybe;

        $last = Arrays::lastValue($labels);
        $hasLeafedValue = $last instanceof TriState;

        if ($hasLeafedValue) {
            \array_pop($labels);
            $leafed = $last;
        } else
            $leafed = TriState::Maybe;

        $edges = [];

        foreach ($labels as $label)
            $edges[] = self::makeMiddleEdgeOf($label);

        return $edges;
    }

    /**
     * @template T
     * @phpstan-param PathEdgeType|PathEdge<T>|T $labelOrPathTypeOrEdge
     * @phpstan-return PathEdge<T>
     */
    private static function makeMiddleEdgeOf(mixed $labelOrPathTypeOrEdge): PathEdge
    {
        if ($labelOrPathTypeOrEdge instanceof PathEdge)
            return $labelOrPathTypeOrEdge;

        if (
            $labelOrPathTypeOrEdge === PathEdgeType::Current ||
            $labelOrPathTypeOrEdge === PathEdgeType::Previous
        ) {
            return self::createSpecialEdge($labelOrPathTypeOrEdge);
        }

        if ($labelOrPathTypeOrEdge instanceof PathEdgeType)
            throw new \InvalidArgumentException("Invalid middle edge type: $labelOrPathTypeOrEdge->name");

        /** @var T $labelOrPathTypeOrEdge */
        return self::createEdge(Optional::of($labelOrPathTypeOrEdge));
    }

    /**
     * @phpstan-return PathEdge<mixed>
     */
    private static function createSpecialEdge(PathEdgeType $type): PathEdge
    {
        return match ($type) {
            PathEdgeType::Current => self::current(Optional::empty()),
            PathEdgeType::Previous => self::previous(Optional::empty()),
        };
    }

    /**
     * Gets a `current` edge.
     * 
     * @phpstan-param Optional<mixed> $label
     * @phpstan-return PathEdge<mixed>
     * 
     * @param Optional $label The label of the edge.
     * @return PathEdge An edge where `$return[PathEdgeType::Current]` evaluate to `true`.
     */
    public static function current(Optional $label): PathEdge
    {
        if ($label->isPresent())
            return self::createEdge($label, PathEdgeType::Current);

        static $mem = self::createEdge(Optional::empty(), PathEdgeType::Current);
        return $mem;
    }

    /**
     * Gets a `previous` edge.
     * 
     * @phpstan-param Optional<mixed> $label
     * @phpstan-return PathEdge<mixed>
     * 
     * @param Optional $label The label of the edge.
     * @return PathEdge An edge where `$return[PathEdgeType::Previous]` evaluate to `true`.
     */
    public static function previous(Optional $label): PathEdge
    {
        if ($label->isPresent())
            return self::createEdge($label, PathEdgeType::Previous);

        static $mem = self::createEdge(Optional::empty(), PathEdgeType::Previous);
        return $mem;
    }

    /**
     * @template T
     * @param Optional<T> $label
     * @return PathEdge<T>
     */
    private static function createEdge(Optional $label, PathEdgeType ...$types): PathEdge
    {
        $types = PathEdges::setOfEnum(...$types);

        if ($label->isPresent())
            return new class($label->get(), $types) extends AbstractPathEdge {

                /**
                 * @param Set<PathEdgeType> $types
                 */
                public function __construct(
                    private mixed $label,
                    private Set $types
                ) {}

                #[\Override]
                public function getType(): Set
                {
                    return $this->types;
                }

                #[\Override]
                public function getLabel(): mixed
                {
                    return $this->label;
                }
            };
        else
            return new class($types) extends AbstractPathEdge {

                /**
                 * @param Set<PathEdgeType> $types
                 */
                public function __construct(
                    private Set $types
                ) {}

                #[\Override]
                public function getType(): Set
                {
                    return $this->types;
                }

                #[\Override]
                public function getLabel(): mixed
                {
                    return null;
                }
            };
    }
}
