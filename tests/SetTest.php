<?php
declare(strict_types = 1);
namespace Time2Split\Help\Tests;

use PHPUnit\Framework\TestCase;
use Time2Split\Help\Sets;

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
}