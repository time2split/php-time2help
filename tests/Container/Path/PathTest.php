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

    public static function provideCanonical(): array
    {
        return [
            [
                ['a', 'b'],
                ['a', 'b'],
                true,
            ],
            [
                [TriState::Yes, 'a', 'b', TriState::Yes],
                [TriState::Yes, 'a', 'b', TriState::Yes],
                true,
            ],
            [
                [PathEdgeType::Current, 'a', PathEdgeType::Current, 'b'],
                ['a', 'b'],
            ],
            [
                ['a', PathEdgeType::Previous, 'b', 'c', PathEdgeType::Previous],
                ['b'],
            ],
        ];
    }

    #[DataProvider('provideCanonical')]
    public final function testCanonical(
        array $pathEdges,
        array $expectedPathEdges,
        bool $isCanonical = false
    ): void {
        $path = Paths::of($pathEdges);
        $canon = $path->canonical();
        $expect = Paths::of($expectedPathEdges);

        if ($isCanonical) {
            $this->assertTrue($path->isCanonical(), 'base is canonical');
        } else {
            $this->assertFalse($path->isCanonical(), 'base is not canonical');
        }
        $this->assertTrue($canon->isCanonical(), 'canon is canonical');
        $this->checkEntriesAreEqual($canon, $expect, self::entryOPathEdgeEquals(...));

        $this->assertSame($path->isRooted(), $canon->isRooted(), 'rooted');
        $this->assertSame($path->isLeafed(), $canon->isLeafed(), 'leafed');
    }

    // ========================================================================

    public static function provideRootLeaf(): array
    {
        return [
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
        ];
    }

    #[DataProvider('provideRootLeaf')]
    public final function testRootLeaf(
        array $pathEdges,
        TriState $rooted = TriState::Maybe,
        TriState $leafed = TriState::Maybe
    ): void {
        $path = Paths::of($pathEdges);
        $this->assertSame($rooted, $path->isRooted(), 'is rooted');
        $this->assertSame($leafed, $path->isLeafed(), 'is leafed');
    }

    // ========================================================================

    public static function provideEdges(): array
    {
        return [
            [
                ['a', 'b'],
                ['a', 'b'],
            ],
            [
                [TriState::Yes, 'a', 'b'],
                ['a', 'b'],
            ],
            [
                ['a', 'b', TriState::Yes],
                ['a', 'b'],
            ],
            [
                [TriState::Yes, 'a', 'b', TriState::Yes],
                ['a', 'b'],
            ],
        ];
    }

    #[DataProvider('provideEdges')]
    public final function testEdges(
        array $pathArray,
        array $expect
    ): void {
        $path = Paths::of($pathArray);

        foreach ($expect as $k => $expectLabel) {
            $pathEdge = $path[$k];
            $this->assertCount(0, $pathEdge);
            $this->assertSame($expectLabel, $pathEdge->getLabel(), 'label');
        }
    }
}
