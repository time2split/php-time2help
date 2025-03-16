<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

use PHPUnit\Framework\TestCase;
use Time2Split\Help\Exception\UnmodifiableSetException;
use Time2Split\Help\Container\Sets;
use Time2Split\Help\Tests\Trait\ArrayAccessUtils;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class SetsTest extends TestCase
{
    use ArrayAccessUtils;
    use SetTestArrayValueTrait;

    #[\Override]
    protected function provideOneUnexistantItem(): mixed
    {
        return '#unexist';
    }

    public final function testNull()
    {
        $null = Sets::null();
        $this->checkEmpty($null);
        $this->assertSame($null, $null->copy());
        $this->assertSame($null, Sets::null());
    }

    public final function testNullException()
    {
        $null = Sets::null();
        $this->expectException(UnmodifiableSetException::class);
        $null['something'] = true;
    }
}
