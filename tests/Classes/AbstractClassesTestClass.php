<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Classes;

use PHPUnit\Framework\TestCase;
use Time2Split\Help\Classes\Copyable;
use Time2Split\Help\Classes\GetNullInstance;
use Time2Split\Help\Classes\GetUnmodifiable;
use Time2Split\Help\Classes\IsUnmodifiable;
use Time2Split\Help\Tests\Container\TestUtils;

/**
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractClassesTestClass extends TestCase
{
    use TestUtils;

    abstract protected static function provideSubject(): mixed;

    public final function testCopyable(): void
    {
        $subject = static::provideSubject();

        if (!($subject instanceof Copyable))
            $this->markTestSkipped();

        $copy = $subject->copy();
        $this->assertNotSame($copy, $subject);
        $this->checkInstanceOf($subject, $copy);
    }

    /*
    final public function testGetNullInstance(): void
    {
        $subject = static::provideSubject();

        if (!($subject instanceof GetNullInstance))
            $this->markTestSkipped();

        $a = static::provideSubject();
        $b = static::provideSubject();
        $null = $a::null();

        $this->checkEmpty($null);
        $this->assertSame($null, $b::null());
        $staticMethod = ($a::class)::null();

        if (\is_callable($staticMethod))
            $this->assertSame($null, $staticMethod());

        if ($subject instanceof Copyable)
            $this->assertSame($null, $null->copy());
    }
    //*/
}
