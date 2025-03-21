<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Iterables;

use ArrayIterator;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Time2Split\Help\Cast\Ensure;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Iterable\ParallelFlag;
use Time2Split\Help\Iterables;
use Time2Split\Help\Tests\TestUtils;

/**
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractIteratorsTestClass extends TestCase
{
    use TestUtils;

    /**
     * The number of entries of the provided iterrable.
     */
    protected const NB_ENTRIES = -1;

    /**
     * Whether the provided iterable can call rewind().
     */
    protected const bool IS_REWINDABLE = true;

    abstract protected static function provideIterable(): iterable;

    // ========================================================================

    public function checkRewind(iterable ...$iterables): void
    {
        if (!static::IS_REWINDABLE)
            return;

        foreach ($iterables as $subject)
            if ($subject instanceof Iterator)
                $subject->rewind();
    }
    // ========================================================================
    // FIRST/LAST
    // ========================================================================

    final public function testFirst(): void
    {
        foreach (static::provideIterable() as $k => $v) {
            $expect = [$k, $v];
            break;
        }
        $expect ??= null;

        $subject = static::provideIterable();
        $isIterator = $subject instanceof Iterator;
        $op = ITerables::first($subject);

        if ($isIterator && static::NB_ENTRIES) {
            // Subject has moved
            $this->assertNotSame($expect, Entry::iteratorCurrent($subject)->toArray());
        }
        foreach ($op as $k => $v) {
            $res = [$k, $v];
            break;
        }
        $res ??= null;

        if ($isIterator) {
            $op->next();
            $this->assertFalse($op->valid());
        }
        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    final public function testLast(): void
    {
        foreach (static::provideIterable() as $k => $v);
        $expect = isset($k) ?
            [$k, $v]
            : null;
        $subject = static::provideIterable();
        $isIterator = $subject instanceof Iterator;
        $op = ITerables::last($subject);

        foreach ($op as $k => $v) {
            $res = [$k, $v];
            break;
        }
        $res ??= null;

        if ($isIterator) {
            $op->next();
            $this->assertFalse($op->valid());
        }
        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    // ========================================================================
    // OPERATIONS
    // ========================================================================

    final public function testAny(): void
    {
        $expect = static::NB_ENTRIES > 0;
        $iterable = static::provideIterable();
        $last = Iterables::lastEntry($iterable);

        $iterable = static::provideIterable();
        $res = Iterables::any($iterable, fn($v) => $v === $last->value);
        $this->assertSame($expect, $res);

        $iterable = static::provideIterable();
        $res = Iterables::any($iterable, fn() => true);
        $this->assertSame($expect, $res);

        $iterable = static::provideIterable();
        $res = Iterables::any($iterable, fn() => false);
        $this->assertFalse($res);
    }

    final public function testAll(): void
    {
        $iterable = static::provideIterable();
        $res = Iterables::all($iterable, fn($v) => isset($v));
        $this->assertTrue($res);

        $iterable = static::provideIterable();
        $res = Iterables::all($iterable, fn() => true);
        $this->assertTrue($res);

        $expect = static::NB_ENTRIES === 0;
        $iterable = static::provideIterable();
        $res = Iterables::all($iterable, fn() => false);
        $this->assertSame($expect, $res);
    }

    // ========================================================================
    // UNARY
    // ========================================================================

    final public function testKeys(): void
    {
        $subject = static::provideIterable();
        $op = Iterables::keys($subject);

        $res = \iterator_to_array($op);
        $expect = [];

        foreach (static::provideIterable() as $k => $v)
            $expect[] = $k;

        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    final public function testValues(): void
    {
        $subject = static::provideIterable();
        $op = Iterables::values($subject);

        $res = \iterator_to_array($op);
        $expect = [];

        foreach (static::provideIterable() as $k => $v)
            $expect[] = $v;

        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    public static function provideLimit(): iterable
    {
        return (function () {
            $max = \min(static::NB_ENTRIES, 10);

            if ($max === 0)
                yield [0, 0];
            else {
                foreach (\range(0, $max - 1) as $offset) {
                    yield [$offset, null];
                    $maxLength = \min(static::NB_ENTRIES, $max, static::NB_ENTRIES - $offset);

                    foreach (\range(0, $maxLength) as $length) {
                        yield [$offset, $length];
                    }
                }
            }
        })();
    }

    #[DataProvider("provideLimit")]
    final public function testLimit(int $offset, ?int $length): void
    {
        $expect = [];
        foreach (static::provideIterable() as $k => $v)
            $expect[] = [$k, $v];

        $expect = \array_slice($expect, $offset, $length);
        $subject = static::provideIterable();
        $op = Iterables::limit($subject, $offset, $length);

        $res = [];
        foreach ($op as $k => $v)
            $res[] = [$k, $v];

        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    // ========================================================================

    final public function testReverse(): void
    {
        $expect = [];
        foreach (static::provideIterable() as $k => $v)
            $expect[] = [$k, $v];

        $subject = static::provideIterable();
        $expect = \array_reverse($expect);
        $op = Iterables::reverse($subject);

        $res = [];
        foreach ($op as $k => $v)
            $res[] = [$k, $v];

        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    final public function testReverseKeys(): void
    {
        $expect = [];
        foreach (static::provideIterable() as $k => $v)
            $expect[] = $k;

        if (!empty($expect)) {
            $expect = \array_reverse($expect);
            $expect = \array_map(
                null,
                \range(0, \count($expect) - 1),
                $expect
            );
        }
        $subject = static::provideIterable();
        $op = Iterables::reverseKeys($subject);

        $res = [];
        foreach ($op as $kk => $k)
            $res[] = [$kk, $k];

        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    final public function testReverseValues(): void
    {
        $expect = [];
        foreach (static::provideIterable() as $v)
            $expect[] = $v;

        if (!empty($expect)) {
            $expect = \array_reverse($expect);
            $expect = \array_map(
                null,
                \range(0, \count($expect) - 1),
                $expect
            );
        }
        $subject = static::provideIterable();
        $op = Iterables::reverseValues($subject);

        $res = [];
        foreach ($op as $k => $v)
            $res[] = [$k, $v];

        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    final public function testFlip(): void
    {
        $expect = [];
        foreach (static::provideIterable() as $k => $v)
            $expect[] = [$v, $k];

        $subject = static::provideIterable();
        $op = Iterables::flip($subject);

        $res = [];
        foreach ($op as $k => $v)
            $res[] = [$k, $v];

        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    final public function testReverseFlip(): void
    {
        $expect = [];
        foreach (static::provideIterable() as $k => $v)
            $expect[] = [$v, $k];

        $expect = \array_reverse($expect);
        $subject = static::provideIterable();
        $op = Iterables::reverseFlip($subject);

        $res = [];
        foreach ($op as $k => $v)
            $res[] = [$k, $v];

        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    // ========================================================================
    // COMBINE MULTIPLE
    // ========================================================================

    final public function testAppend(): void
    {
        $expect = [];
        foreach (static::provideIterable() as $k => $v)
            $expect[] = [$k, $v];

        $expect = array_merge($expect, $expect);

        $op = Iterables::append(
            $sa = static::provideIterable(),
            $sb = static::provideIterable()
        );
        $res = [];
        foreach ($op as $k => $v)
            $res[] = [$k, $v];

        $this->assertSame($expect, $res);
        $this->checkRewind($sa, $sb, $op);
    }

    final public function testCombine(): void
    {
        $expect = [];
        foreach (static::provideIterable() as $k => $v)
            $expect[] = [$k, $v];

        $sa = static::provideIterable();
        $sb = static::provideIterable();
        $op = Iterables::combine(Iterables::keys($sa), $sb);

        $this->checkIterablesEquals(static::provideIterable(), $op);
        $this->checkRewind($expect, $op);
        $this->checkRewind($sa, $sb, $op);
    }

    public static function provideParallelFlag(): iterable
    {
        return [[ParallelFlag::NEED_ANY]];
        return [[ParallelFlag::NEED_ALL], [ParallelFlag::NEED_ANY]];
    }

    protected final static function makeParallelExpectation(
        ParallelFlag $flag,
        iterable $a,
        iterable $b,
        callable $makeResult
    ): array {
        $ita = Ensure::iterator($a);
        $itb = Ensure::iterator($b);
        $ita->rewind();
        $itb->rewind();
        $va = $ita->valid();
        $vb = $itb->valid();

        $expect = [];
        while ($va || $vb) {
            if (
                !($va && $vb) &&
                $flag === ParallelFlag::NEED_ALL
            ) break;
            $ea = Entry::iteratorCurrent($ita);
            $eb = Entry::iteratorCurrent($itb);
            $res = $makeResult($ea, $eb, $expect);

            if ($res instanceof Iterator) {
                foreach ($res as $v)
                    $expect[] = $v;
            } else
                $expect[] = $res;
            $ita->next();
            $itb->next();
            $va = $ita->valid();
            $vb = $itb->valid();
        }
        return $expect;
    }

    #[DataProvider("provideParallelFlag")]
    final public function testMultiple(ParallelFlag $flag): void
    {
        if (static::NB_ENTRIES < 5)
            $this->markTestSkipped(sprintf("NB_ENTRIES(%d) < 5", static::NB_ENTRIES));

        $a = fn() => Iterables::limit(static::provideIterable(), 0, 3);
        $b = fn() => Iterables::limit(static::provideIterable(), 3, 2);
        $op = Iterables::multiple([$sa = $a(), $sb = $b()], $flag);

        foreach ($op as $k => $v)
            $res[] = [$k, $v];

        $expect = self::makeParallelExpectation(
            $flag,
            $a(),
            $b(),
            fn(Entry $a, Entry $b) => [[$a->key, $b->key], [$a->value, $b->value]]
        );
        $this->assertSame($expect, $res);
        $this->checkRewind($sa, $sb, $op);
    }

    #[DataProvider("provideParallelFlag")]
    final public function testParallel(ParallelFlag $flag): void
    {
        if (static::NB_ENTRIES < 5)
            $this->markTestSkipped(sprintf("NB_ENTRIES(%d) < 5", static::NB_ENTRIES));

        $a = fn() => Iterables::limit(static::provideIterable(), 0, 3);
        $b = fn() => Iterables::limit(static::provideIterable(), 3, 2);
        $op = Iterables::parallel([$sa = $a(), $sb = $b()], $flag);

        foreach ($op as $k => $v)
            $res[] = [$k, $v];

        $expect = self::makeParallelExpectation(
            $flag,
            $a(),
            $b(),
            fn(Entry $a, Entry $b) => new ArrayIterator([
                $a->toArray(),
                $b->toArray(),
            ])
        );
        $this->assertSame($expect, $res);
        $this->checkRewind($sa, $sb, $op);
    }

    #[DataProvider("provideParallelFlag")]
    final public function testParallelChunk(ParallelFlag $flag): void
    {
        if (static::NB_ENTRIES < 5)
            $this->markTestSkipped(sprintf("NB_ENTRIES(%d) < 5", static::NB_ENTRIES));

        $a = fn() => Iterables::limit(static::provideIterable(), 0, 3);
        $b = fn() => Iterables::limit(static::provideIterable(), 3, 2);
        $op = Iterables::parallelChunk([$sa = $a(), $sb =  $b()], flag: $flag);

        foreach ($op as $chunk) {
            $sub = [];
            foreach ($chunk as $k => $v)
                $sub[] = [$k, $v];
            $res[] = $sub;
        }
        $expect = self::makeParallelExpectation(
            $flag,
            $a(),
            $b(),
            fn(Entry $a, Entry $b) =>  [$a->toArray(), $b->toArray()]
        );
        $this->assertSame($expect, $res);
        $this->checkRewind($sa, $sb, $op);
    }

    // ========================================================================
    // MAP
    // ========================================================================

    protected static function mapOne(mixed $item): mixed
    {
        return match (\gettype($item)) {
            'int' => $item * 2,
            'string' => "$item.$item",
            default => (string)$item,
        };
    }

    final public function testMap(): void
    {
        $expect = [];
        foreach (static::provideIterable() as $k => $v)
            $expect[] = [static::mapOne($k), static::mapOne($v)];

        $subject = static::provideIterable();
        $op = Iterables::map(
            $subject,
            static::mapOne(...),
            static::mapOne(...)
        );

        $res = [];
        foreach ($op as $k => $v)
            $res[] = [$k, $v];

        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    final public function testMapKey(): void
    {
        $expect = [];
        foreach (static::provideIterable() as $k => $v)
            $expect[] = [static::mapOne($k), $v];

        $subject = static::provideIterable();
        $op = Iterables::mapKey(
            $subject,
            static::mapOne(...)
        );

        $res = [];
        foreach ($op as $k => $v)
            $res[] = [$k, $v];

        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }

    final public function testMapValue(): void
    {
        $expect = [];
        foreach (static::provideIterable() as $k => $v)
            $expect[] = [$k, static::mapOne($v)];

        $subject = static::provideIterable();
        $op = Iterables::mapValue(
            $subject,
            static::mapOne(...)
        );

        $res = [];
        foreach ($op as $k => $v)
            $res[] = [$k, $v];

        $this->assertSame($expect, $res);
        $this->checkRewind($subject, $op);
    }
}
