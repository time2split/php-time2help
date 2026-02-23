<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Time2Split\Help\Optional;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class OptionalTest extends TestCase
{
    public function testOptionalOf()
    {
        $val = 0;
        $opt = Optional::of($val);
        $this->assertTrue($opt->isPresent());
        $this->assertSame($val, $opt->get());
        $this->assertSame($val, $opt->orElse(null));
        $this->assertSame($val, $opt->orElseGet(fn() => null));
        $this->assertNull(Optional::of(null)->get());
    }

    public function testOptionalEmpty()
    {
        $opt = Optional::empty();
        $this->assertFalse($opt->isPresent());
        $this->assertNull($opt->orElse(null));
        $this->assertNull($opt->orElseGet(fn() => null));
        $this->assertSame($opt, Optional::empty());
    }

    public function testOptionalOfNullable()
    {
        $this->assertTrue(Optional::ofNullable(1)->isPresent());
        $this->assertFalse(Optional::ofNullable(null)->isPresent());
    }

    public function testOptionalNoReference()
    {
        $this->assertFalse(Optional::empty()->isReference());
        $this->assertFalse(Optional::ofNullable(null)->isReference());
        $this->assertFalse(Optional::ofNullable(0)->isReference());
        $this->assertFalse(Optional::of(0)->isReference());
    }

    public function testOptionalEmptySameRef()
    {
        $empty = Optional::empty();
        $ref = null;
        $this->assertSame($empty, Optional::empty());
        $this->assertSame($empty, Optional::ofNullable(null));
        $this->assertSame($empty, Optional::ofNullableRef($ref));
    }

    public static function provideOptionalGetException(): array
    {
        return [
            [fn() => Optional::empty()],
            [fn() => Optional::ofNullable(null)],
        ];
    }

    #[DataProvider("provideOptionalGetException")]
    public function testOptionalGetException(\Closure $construct)
    {
        $this->expectException(\Error::class);
        $construct()->get();
    }

    // ========================================================================

    public function testOptionalRef(): void
    {
        $var = 0;
        $opt = Optional::ofRef($var);
        $last = $var;

        $this->assertTrue($opt->isReference());
        $ref = &$opt->getRef();
        $ref = 5;
        $this->assertSame($ref, $var);
        $this->assertNotSame($last, $var);
        $last = $var;
        unset($ref);

        @$ref = &$opt->get();
        $ref = 10;
        $this->assertSame($last, $var);
        $this->assertNotSame($var, $ref);
        unset($ref);
    }

    public static function provideOptionalRefGetException(): array
    {
        return [
            [fn() => Optional::empty()],
            [fn() => Optional::ofNullable(null)],
            [fn() => Optional::of(0)],
        ];
    }

    #[DataProvider("provideOptionalRefGetException")]
    public function testOptionalRefGetException(\Closure $construct)
    {
        $this->expectException(\Error::class);
        $construct()->getRef();
    }
}
