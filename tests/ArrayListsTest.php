<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Time2Split\Help\ArrayLists;

final class ArrayListsTest extends TestCase
{
    private const array_abc = [
        'a' => 1,
        'b' => 2,
        'c' => 3
    ];
    private const list_abc = [
        'a',
        'b',
        'c'
    ];
    private const alist_abc = [
        1 => 'a',
        'b',
        'c'
    ];

    public function testIsList(): void
    {
        $this->assertTrue(ArrayLists::isList(self::list_abc));
        $this->assertFalse(ArrayLists::isList(self::alist_abc));
        $this->assertFalse(ArrayLists::isList(self::array_abc));
    }

    public function testIsAlmostList(): void
    {
        $this->assertTrue(ArrayLists::isAlmostList(self::list_abc));
        $this->assertTrue(ArrayLists::isAlmostList(self::alist_abc));
        $this->assertFalse(ArrayLists::isAlmostList(self::array_abc));
    }

    public function testAlmostListToList(): void
    {
        $this->assertSame(self::list_abc, ArrayLists::almostListToList(self::list_abc));
        $this->assertSame(self::list_abc, ArrayLists::almostListToList(self::alist_abc));

        $this->expectException(\InvalidArgumentException::class);
        ArrayLists::almostListToList(self::array_abc);
    }

    public static function provideAlmostListToListException(): array
    {
        return [
            [ArrayLists::almostListToList(...), \InvalidArgumentException::class],
            [ArrayLists::tryAlmostListToList(...), \InvalidArgumentException::class],
            [fn($l) => ArrayLists::tryAlmostListToList($l, fn() => throw new \Error()), \Error::class],
        ];
    }
    #[DataProvider("provideAlmostListToListException")]
    public function testAlmostListToListException(Closure $toList, string $expect): void
    {
        $this->expectException($expect);
        $toList(self::array_abc);
    }

    public function testMutateToList(): void
    {
        $subject = self::array_abc;
        ArrayLists::mutateToList($subject);
        $this->assertSame(self::array_abc, $subject, 'array no changes');
        ArrayLists::mutateToListRecursive($subject);
        $this->assertSame(self::array_abc, $subject, 'array no changes');

        $subject = self::list_abc;
        ArrayLists::mutateToList($subject);
        $this->assertSame(self::list_abc, $subject, 'list no changes');
        ArrayLists::mutateToListRecursive($subject);
        $this->assertSame(self::list_abc, $subject, 'list no changes');

        $subject = self::alist_abc;
        ArrayLists::mutateToList($subject);
        $this->assertSame(self::list_abc, $subject, 'almost list changes');
        ArrayLists::mutateToListRecursive($subject);
        $this->assertSame(self::list_abc, $subject, 'almost list changes');
    }

    public static function provideMutateToListRecursive(): array
    {
        return [
            [
                [1 => 'a', 5 => [1 => 'A', 'B']],
                ['a', ['A', 'B']],
            ],
            [
                [1 => 'a', 'x' => [1 => 'A', 'B']],
                [1 => 'a', 'x' => ['A', 'B']],
            ],
        ];
    }

    #[DataProvider("provideMutateToListRecursive")]
    public function testMutateToListRecursive(array $subject, array $expect): void
    {
        $this->assertNotSame($expect, $subject);
        ArrayLists::mutateToListRecursive($subject);
        $this->assertSame($expect, $subject);
    }
}
