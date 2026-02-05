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
     * @template T
     * @phpstan-param iterable<T|PathEdgeType|PathEdge<T>> $labelOrPathTypeOrEdgeIt
     * @phpstan-return PathEdge<T>[]
     * @internal
     */
    public static function listOf(
        iterable $labelOrPathTypeOrEdgeIt,
    ): array {
        $labels = \iterator_to_array($labelOrPathTypeOrEdgeIt);

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

        $edges = [$rooted];

        foreach ($labels as $label)
            $edges[] = self::makeMiddleEdgeOf($label);

        $edges[] = $leafed;
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
     * @phpstan-return PathEdge<mixed>
     */
    public static function current(Optional $label): PathEdge
    {
        if ($label->isPresent())
            return self::createEdge($label, PathEdgeType::Current);

        static $mem = self::createEdge(Optional::empty(), PathEdgeType::Current);
        return $mem;
    }

    /**
     * @phpstan-return PathEdge<mixed>
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
