<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Time2Split\Help\Bag;
use Time2Split\Help\Bags;
use Time2Split\Help\Exception\UnmodifiableSetException;

final class BagTest extends TestCase
{
    public static function bagProvider(): iterable
    {
        return [
            'arrayKeys' => [fn() => Bags::arrayKeys()],
            'toArrayKeys' => [fn() => Bags::toArrayKeys(fn($e) => $e, fn($e) => $e)],
        ];
    }

    #[DataProvider('bagProvider')]
    public function testArrayKeys(callable $getABag): void
    {
        $bag = $getABag();

        $this->assertFalse(isset($bag['a']));
        $this->assertSame(0, \count($bag));

        $bag['a'] = true;
        $this->assertTrue(isset($bag['a']));
        $this->assertSame(1, \count($bag));
        $this->assertSame([
            'a'
        ], \iterator_to_array($bag));

        $bag['a'] = 2;
        $bag['b'] = true;
        $this->assertTrue(isset($bag['a']));
        $this->assertSame(4, \count($bag));
        $this->assertSame(3, $bag['a']);
        $this->assertSame(['a', 'a', 'a', 'b'], \iterator_to_array($bag));

        // Unset
        $bag->clear();
        $this->assertSame(0, \count($bag));
        $this->assertSame(0, $bag['a']);

        $bag['a'] = 1;
        unset($bag['a']);
        $this->assertFalse(isset($bag['a']));
        $this->assertSame(0, \count($bag));

        $bag['a'] = true;
        $bag['a'] = false;
        $this->assertFalse(isset($bag['a']));
        $this->assertSame(0, \count($bag));

        $bag->setMore(0, 1, 2, 3);
        $this->assertSame(4, \count($bag));
        $bag->unsetMore(1, 2);
        $this->assertSame(2, \count($bag));
        $this->assertSame([
            0,
            3
        ], \iterator_to_array($bag));
    }

    // ========================================================================
    public static function _testUnmodifiable(): iterable
    {
        return [
            [
                (function ($bag) {
                    $bag[4] = true;
                })
            ],
            [
                (function ($bag) {
                    $bag[4] = false;
                })
            ],
            [
                (function ($bag) {
                    unset($bag[4]);
                })
            ]
        ];
    }

    #[DataProvider('_testUnmodifiable')]
    public function testUnmodifiable(\Closure $test)
    {
        $bag = Bags::arrayKeys();
        $bag->setMore(0, 1, 2, 3);
        $bag = Bags::unmodifiable($bag);
        $this->expectException(UnmodifiableSetException::class);
        $test($bag);
    }

    // ========================================================================
    public function testNull()
    {
        $bag = Bags::null();

        $this->assertSame(0, \count($bag));
        $this->assertSame(0, $bag['a']);
        $this->assertSame([], \iterator_to_array($bag));

        $this->assertSame($bag, Bags::null());
    }

    #[DataProvider('_testUnmodifiable')]
    public function testNullException(\Closure $test)
    {
        $bag = Bags::null();
        $this->expectException(UnmodifiableSetException::class);
        $test($bag);
    }

    // ========================================================================

    public static function enumProvider(): iterable
    {
        return [
            [Bags::ofEnum(ABagUnitEnum::class)],
            [Bags::ofEnum(ABagUnitEnum::a)],
        ];
    }

    #[Test]
    #[DataProvider('enumProvider')]
    public function enum(Bag $bag)
    {
        $this->assertSame(0, $bag[ABagUnitEnum::a]);
        $bag[ABagUnitEnum::a] = 2;
        $this->assertSame(2, $bag[ABagUnitEnum::a]);
        $this->assertSame([
            ABagUnitEnum::a,
            ABagUnitEnum::a,
        ], \iterator_to_array($bag));
        $bag[ABagUnitEnum::a] = false;
        $this->assertSame(1, $bag[ABagUnitEnum::a]);
    }

    // ========================================================================

    public static function _testBackedEnum(): iterable
    {
        return [
            [Bags::ofBackedEnum(ABagEnum::class)],
            [Bags::ofBackedEnum(ABagEnum::a)],
            [Bags::ofEnum(ABagEnum::class)],
            [Bags::ofEnum(ABagEnum::a)],
        ];
    }

    #[DataProvider('_testBackedEnum')]
    public function testBackedEnum(Bag $bag)
    {
        $this->assertSame(0, $bag[ABagEnum::a]);
        $bag[ABagEnum::a] = true;
        $this->assertSame(1, $bag[ABagEnum::a]);
        $this->assertSame([
            ABagEnum::a
        ], \iterator_to_array($bag));
        $bag[ABagEnum::a] = false;
        $this->assertSame(0, $bag[ABagEnum::a]);
    }

    public static function _testBackedEnumException(): iterable
    {
        return [
            [
                (function ($bag) {
                    $bag[AnotherBagEnum::a] = true;
                })
            ],
            [
                (function ($bag) {
                    Bags::ofBackedEnum('badClass');
                })
            ]
        ];
    }

    #[DataProvider('_testBackedEnumException')]
    public function testBackedEnumException(\Closure $test)
    {
        $bag = Bags::ofBackedEnum(ABagEnum::class);
        $this->expectException(\InvalidArgumentException::class);
        $test($bag);
    }

    // ========================================================================

    #[Test]
    public function equals()
    {
        $a = Bags::arrayKeys()->setMore(0, 1, 2, 2);
        $b = $a;
        $this->assertTrue(Bags::equals($a, $b), 'Not the sames');

        // Must be order independant
        $b = Bags::arrayKeys()->setMore(2, 1, 0, 2);
        $this->assertTrue(Bags::equals($a, $b), 'Order dependency');

        $b = Bags::arrayKeys()->setMore(0, 1, 3);
        $this->assertFalse(Bags::equals($a, $b), 'Are equals');
    }

    #[Test]
    public function includedIn()
    {
        $a = Bags::arrayKeys()->setMore(0, 1, 2, 2);
        $b = $a;
        $this->assertTrue(Bags::includedIn($a, $b), 'Not the sames');

        // Must be order independant
        $b = Bags::arrayKeys()->setMore(2, 1, 0, 2);
        $this->assertTrue(Bags::includedIn($a, $b), 'Order dependency');
        $this->assertTrue(Bags::includedIn($b, $a), 'Order dependency');

        $a = Bags::arrayKeys()->setMore(0, 2);
        $this->assertTrue(Bags::includedIn($a, $b), 'Is not included');
        $this->assertFalse(Bags::includedIn($b, $a), 'Is included');

        $a = Bags::arrayKeys()->setMore(0, 3);
        $this->assertFalse(Bags::includedIn($a, $b), 'Is included');

        $a = Bags::arrayKeys()->setMore(0, 1, 3);
        $this->assertFalse(Bags::includedIn($a, $b), 'Is included');
    }
}

enum ABagUnitEnum
{

    case a;
}

enum ABagEnum: int
{

    case a = 0;
}

enum AnotherBagEnum: int
{

    case a = 0;
}
