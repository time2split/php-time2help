<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use Time2Split\Help\Container\ContainerAA;
use Time2Split\Help\Functions;

/**
 * @author Olivier Rodriguez (zuri)
 */
trait ContainerAATestUtils
{
    abstract protected static function arrayValueIsAbsent(mixed $value): bool;
    abstract protected static function arrayValueIsPresent(mixed $value): bool;

    protected static function makeSubjectOffsetValueValueForEqualityTest(mixed $value): mixed
    {
        return $value;
    }

    protected final function checkOffsetNotExists(ContainerAA $subject, $offset): void
    {
        $h = __FUNCTION__ . '/';
        $item_s = Functions::basicToString($offset);
        $this->assertTrue($this::arrayValueIsAbsent($subject[$offset]), "{$h}offsetGet($item_s)");
        $this->assertFalse($subject->offsetExists($offset), "{$h}offsetExists($item_s)");
        $this->assertFalse(isset($subject[$offset]), "{$h}isset($item_s)");
    }

    protected final function checkOffsetExists(ContainerAA $subject, mixed $offset): void
    {
        $h = __FUNCTION__ . '/';
        $item_s = Functions::basicToString($offset);
        $this->assertTrue($this::arrayValueIsPresent($subject[$offset]), "{$h}offsetGet($item_s)");
        $this->assertTrue($subject->offsetExists($offset), "{$h}offsetExists($item_s)");
        $this->assertTrue(isset($subject[$offset]), "{$h}isset($item_s)");
    }

    protected final function checkOffsetValue(ContainerAA $subject, mixed $offset, mixed $value, bool $strict = false): void
    {
        $h = __FUNCTION__ . '/';
        $item_s = Functions::basicToString($offset);
        $this->assertTrue($subject->offsetExists($offset), "{$h}offsetExists($item_s)");

        $subjectValue = $subject[$offset];
        $subjectValue = static::makeSubjectOffsetValueValueForEqualityTest($subjectValue);

        if ($strict)
            $this->assertSame($value, $subjectValue, "{$h}same($item_s)");
        else
            $this->assertEquals($value, $subjectValue, "{$h}equals($item_s)");
    }
}
