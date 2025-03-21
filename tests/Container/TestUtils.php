<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use ArrayAccess;
use Time2Split\Help\Functions;
use Time2Split\Help\Tests\TestUtils as TestsTestUtils;

/**
 * @author Olivier Rodriguez (zuri)
 */
trait TestUtils
{
    use TestsTestUtils;

    abstract protected static function arrayValueIsAbsent(mixed $value): bool;
    abstract protected static function arrayValueIsPresent(mixed $value): bool;

    protected function checkOffsetNotExists(ArrayAccess $subject, $offset): void
    {
        $h = __FUNCTION__ . '/';
        $item_s = Functions::basicToString($offset);
        $this->assertTrue($this::arrayValueIsAbsent($subject[$offset]), "{$h}offsetGet($item_s)");
        $this->assertFalse($subject->offsetExists($offset), "{$h}offsetExists($item_s)");
        $this->assertFalse(isset($subject[$offset]), "{$h}isset($item_s)");
    }

    protected function checkOffsetExists(ArrayAccess $subject, mixed $offset): void
    {
        $h = __FUNCTION__ . '/';
        $item_s = Functions::basicToString($offset);
        $this->assertTrue($this::arrayValueIsPresent($subject[$offset]), "{$h}offsetGet($item_s)");
        $this->assertTrue($subject->offsetExists($offset), "{$h}offsetExists($item_s)");
        $this->assertTrue(isset($subject[$offset]), "{$h}isset($item_s)");
    }

    protected function checkOffsetValue(ArrayAccess $subject, mixed $offset, mixed $value, bool $strict = true): void
    {
        $h = __FUNCTION__ . '/';
        $item_s = Functions::basicToString($offset);
        $this->assertTrue($subject->offsetExists($offset), "{$h}offsetExists($item_s)");

        if ($strict)
            $this->assertSame($value, $subject[$offset], "{$h}isset($item_s)");
        else
            $this->assertEquals($value, $subject[$offset], "{$h}isset($item_s)");
    }
}
