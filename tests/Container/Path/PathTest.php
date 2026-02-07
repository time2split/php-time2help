<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Path;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Container\Path;
use Time2Split\Help\Container\PathEdge;
use Time2Split\Help\Container\PathEdgeType;
use Time2Split\Help\Container\Paths;
use Time2Split\Help\Functions;
use Time2Split\Help\Tests\Container\AbstractArrayAccessContainerTestClass;
use Time2Split\Help\TriState;

class PathTest extends AbstractArrayAccessContainerTestClass
{
    protected static function provideEntries(): array
    {
        return [
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
        ];
    }
    protected static function makeSubjectOffsetValueValueForEqualityTest(mixed $value): mixed
    {
        return $value->getLabel();
    }

    #[\Override]
    protected static function entriesEqualClosure_traversableTest(bool $strict = false): Closure
    {
        return self::entriesEquals($strict);
    }

    #[\Override]
    protected static function entriesEqualClosure_putMethodTest(bool $strict = false): Closure
    {
        return self::entriesEquals($strict);
    }

    private static function entriesEquals(bool $strict): Closure
    {
        $eq = Functions::getCallbackForEquals($strict);

        return function (Entry $a, Entry $b) use ($eq): bool {
            /** @var PathEdge */
            return $eq($a->value, $b->value->getLabel());
        };
    }

    private function entryOPathEdgeEquals(Entry $a, Entry $b): bool
    {
        $aPathEdge = $a->value;
        $bPathEdge = $b->value;

        return $aPathEdge->getType()->equals($bPathEdge->getType());
    }

    #[\Override]
    protected static function arrayValueIsAbsent(mixed $value): bool
    {
        return null === $value;
    }

    #[\Override]
    protected static function arrayValueIsPresent(mixed $value): bool
    {
        return null !== $value;
    }

    #[\Override]
    protected static function provideContainer(): Path
    {
        return Paths::of([]);
    }

    #[\Override]
    protected static function provideContainerWithSubEntries(int $offset = 0, ?int $length = null): Path
    {
        $entries = static::provideSubEntries($offset, $length);
        return Paths::of(Entry::traverseEntries($entries));
    }

    // ========================================================================

    public static function provideRootLeaf(): array
    {
        return [
            [
                [],
                TriState::Maybe,
                TriState::Maybe,
            ],
            [
                ['a', 'b'],
            ],
            [
                [TriState::Yes, 'a', 'b'],
                TriState::Yes,
            ],
            [
                ['a', 'b', TriState::Yes],
                TriState::Maybe,
                TriState::Yes,
            ],
            [
                [TriState::Yes, 'a', 'b', TriState::Yes],
                TriState::Yes,
                TriState::Yes,
            ],
            [
                [PathEdgeType::Current],
                TriState::Maybe,
                TriState::No,
            ],
            [
                ['a', PathEdgeType::Current],
                TriState::Maybe,
                TriState::No,
            ],
            [
                [PathEdgeType::Current, 'a'],
                TriState::Maybe,
                TriState::Maybe,
            ],
            [
                [PathEdgeType::Previous],
                TriState::Maybe,
                TriState::No,
            ],
            [
                ['a', PathEdgeType::Previous],
                TriState::Maybe,
                TriState::No,
            ],
            [
                [PathEdgeType::Previous, 'a'],
                TriState::Maybe,
                TriState::Maybe,
            ],
        ];
    }

    #[DataProvider('provideRootLeaf')]
    public final function testRootLeaf(
        array $edges,
        TriState $rooted = TriState::Maybe,
        TriState $leafed = TriState::Maybe
    ): void {
        $path = Paths::of($edges);
        $expectEdges = $edges;
        $expectEdges = \array_filter($expectEdges, fn($v) => !($v instanceof TriState));
        $expectEdges = \array_values($expectEdges);

        $this->assertSame($rooted, $path->rooted(), 'rooted');
        $this->assertSame($leafed, $path->leafed(), 'leafed');

        $expect = Paths::of($expectEdges);
        $this->checkEntriesAreEqual($expect, $path, self::entryOPathEdgeEquals(...));
        $this->assertSame($expect->toListOfElements(), $path->toListOfElements());
        $this->assertSame($expectEdges, $path->toListOfElements());
    }

    // ========================================================================

    public static function provideCanon(): array
    {
        return [
            '../a' => [
                [PathEdgeType::Previous, 'a'],
                TriState::Maybe,
                TriState::Maybe,
                [PathEdgeType::Previous, 'a'],
            ],
            '/../a' => [
                [TriState::Yes, PathEdgeType::Previous, 'a'],
                TriState::Yes,
                TriState::Maybe,
                [PathEdgeType::Previous, 'a'],
            ],
            'a/..' => [
                ['a', PathEdgeType::Previous],
                TriState::Maybe,
                TriState::No,
                [],
            ],
            '/a/..' => [
                [TriState::Yes, 'a', PathEdgeType::Previous],
                TriState::Yes,
                TriState::No,
                [],
            ],
            './a' => [
                [PathEdgeType::Current, 'a'],
                TriState::Maybe,
                TriState::Maybe,
                ['a'],
            ],
            '/./a' => [
                [TriState::Yes, PathEdgeType::Current, 'a'],
                TriState::Yes,
                TriState::Maybe,
                ['a'],
            ],
            'a/.' => [
                ['a', PathEdgeType::Current],
                TriState::Maybe,
                TriState::No,
                ['a'],
            ],
            '/a/.' => [
                [TriState::Yes, 'a', PathEdgeType::Current],
                TriState::Yes,
                TriState::No,
                ['a'],
            ],
            [
                ['a', 'b'],
                'isCanonical' => true,
            ],
            [
                [TriState::Yes, 'a', 'b', TriState::Yes],
                TriState::Yes,
                TriState::Yes,
                'isCanonical' => true,
            ],
            [
                [PathEdgeType::Current, 'a', PathEdgeType::Current, 'b'],
                'expectEdges' => ['a', 'b'],
            ],
            [
                ['a', PathEdgeType::Previous, 'b', 'c', PathEdgeType::Previous],
                'leafed' => TriState::No,
                'expectEdges' => ['b'],
            ],
        ];
    }

    #[DataProvider('provideCanon')]
    public final function testCanon(
        array $edges,
        TriState $rooted = TriState::Maybe,
        TriState $leafed = TriState::Maybe,
        ?array $expectEdges = null,
        bool $isCanonical = false,
    ): void {
        $path = Paths::of($edges);
        $canon = $path->canonical();

        if (null === $expectEdges) {
            $expectEdges = $edges;
            $expectEdges = \array_filter($expectEdges, fn($v) => !($v instanceof TriState));
            $expectEdges = \array_values($expectEdges);
        }

        if ($isCanonical) {
            $this->assertTrue($path->isCanonical(), 'base is canonical');
        } else {
            $this->assertFalse($path->isCanonical(), 'base is not canonical');
        }
        $this->assertSame($rooted, $canon->rooted(), 'rooted');
        $this->assertSame($leafed, $canon->leafed(), 'leafed');

        $expect = Paths::of($expectEdges);
        $this->checkEntriesAreEqual($expect, $canon, self::entryOPathEdgeEquals(...));
        $this->assertSame($expect->toListOfElements(), $canon->toListOfElements());
        $this->assertSame($expectEdges, $canon->toListOfElements());
    }

    // ========================================================================

    public final function testEdges(): void
    {
        $edges = ['a', PathEdgeType::Current, PathEdgeType::Previous];
        $path = Paths::of($edges);

        $edge = $path[0];
        $this->assertCount(0, $edge);
        $this->assertSame('a', $edge->getLabel());
        $this->assertEmpty($edge->getType());

        $edge = $path[1];
        $type = $edge->getType();
        $this->assertCount(1, $edge);
        $this->assertCount(1, $type);
        $this->assertTrue($edge[PathEdgeType::Current]);
        $this->assertTrue($type[PathEdgeType::Current]);
        $this->assertFalse($edge[PathEdgeType::Previous]);
        $this->assertFalse($type[PathEdgeType::Previous]);
        $this->assertSame(PathEdgeType::Current, $edge->getLabel());

        $edge = $path[2];
        $type = $edge->getType();
        $this->assertCount(1, $edge);
        $this->assertCount(1, $type);
        $this->assertTrue($edge[PathEdgeType::Previous]);
        $this->assertTrue($type[PathEdgeType::Previous]);
        $this->assertFalse($edge[PathEdgeType::Current]);
        $this->assertFalse($type[PathEdgeType::Current]);
        $this->assertSame(PathEdgeType::Previous, $edge->getLabel());
    }
}
