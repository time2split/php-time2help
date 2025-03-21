<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Cast;

use PHPUnit\Framework\TestCase;
use Time2Split\Help\Cast\Ensure;

final class CastTest extends TestCase
{
    public function testIterableList(): void
    {
        foreach ([null, 0, 0.0, "", true] as $value) {
            $list = Ensure::iterableList($value);
            $this->assertSame([$value], $list);
        }
        $l = [1, 2, 3];
        $list = Ensure::iterableList($l);
        $this->assertSame($l, $list);

        // Same object
        $l = new \ArrayIterator(['a' => 1, 'b' => 2]);
        $list = Ensure::iterableList($l);
        $this->assertSame(\array_values($l->getArrayCopy()), \iterator_to_array($list));
    }

    public function testIterable(): void
    {
        foreach (
            [null, 0, 0.0, "", true] as $value
        ) {
            $it = Ensure::iterable($value);
            $this->assertSame([$value], $it);
        }
        foreach (
            [
                ['a' => 0, 'b' => 1, 'c' => 2],
                new \ArrayIterator(['a', 'b', 'c']),
            ] as $value
        ) {
            $it = Ensure::iterable($value);
            $this->assertSame($value, $it);
        }
    }

    public function testIterator(): void
    {
        foreach (
            [null, 0, 0.0, "", true] as $value
        ) {
            $it = Ensure::iterator($value);
            $this->assertInstanceOf(\Iterator::class, $it);
            $this->assertSame([$value], \iterator_to_array($it));
        }
        foreach (
            [
                ['a' => 0, 'b' => 1, 'c' => 2],
                new \ArrayIterator(['a', 'b', 'c']),
                \SplFixedArray::fromArray([0,  1, 2]),
            ] as $value
        ) {
            $it = Ensure::iterator($value);
            $this->assertInstanceOf(\Iterator::class, $it);
            $this->assertSame(\iterator_to_array($value), \iterator_to_array($it));
        }
    }
}
