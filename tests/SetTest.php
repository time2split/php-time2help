<?php
declare(strict_types = 1);
namespace Time2Split\Help\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Time2Split\Help\Sets;
use Time2Split\Help\Exception\UnmodifiableSetException;

final class SetTest extends TestCase
{

    public function testArrayKeys(): void
    {
        $set = Sets::arrayKeys();

        $this->assertFalse(isset($set['a']));
        $this->assertSame(0, \count($set));

        $set['a'] = true;
        $this->assertTrue(isset($set['a']));
        $this->assertSame(1, \count($set));
        $this->assertSame([
            'a'
        ], \iterator_to_array($set));

        // Unset

        unset($set['a']);
        $this->assertFalse(isset($set['a']));
        $this->assertSame(0, \count($set));

        $set['a'] = true;
        $set['a'] = false;
        $this->assertFalse(isset($set['a']));
        $this->assertSame(0, \count($set));

        $set->setMore(0, 1, 2, 3);
        $this->assertSame(4, \count($set));
        $set->unsetMore(1, 2);
        $this->assertSame(2, \count($set));
        $this->assertSame([
            0,
            3
        ], \iterator_to_array($set));
    }

    // ========================================================================
    public static function _testUnmodifiable(): iterable
    {
        return [
            [
                (function ($set) {
                    $set[4] = true;
                })
            ],
            [
                (function ($set) {
                    $set[4] = false;
                })
            ],
            [
                (function ($set) {
                    unset($set[4]);
                })
            ]
        ];
    }

    #[DataProvider('_testUnmodifiable')]
    public function testUnmodifiable(\Closure $test)
    {
        $set = Sets::arrayKeys();
        $set->setMore(0, 1, 2, 3);
        $set = Sets::unmodifiable($set);
        $this->expectException(UnmodifiableSetException::class);
        $test($set);
    }

    // ========================================================================
    public function testNull()
    {
        $set = Sets::null();

        $this->assertSame(0, \count($set));
        $this->assertFalse($set['a']);
        $this->assertSame([], \iterator_to_array($set));

        $this->assertSame($set, Sets::null());
    }

    #[DataProvider('_testUnmodifiable')]
    public function testNullException(\Closure $test)
    {
        $set = Sets::null();
        $this->expectException(UnmodifiableSetException::class);
        $test($set);
    }
}