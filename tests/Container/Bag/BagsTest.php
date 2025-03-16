<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Bag;

use PHPUnit\Framework\TestCase;
use Time2Split\Help\Bags;
use Time2Split\Help\Exception\UnmodifiableSetException;
use Time2Split\Help\Tests\Trait\ArrayAccessUtils;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class BagsTest extends TestCase
{
    use ArrayAccessUtils;
    use BagTestArrayValueTrait;

    #[\Override]
    protected function provideOneUnexistantItem(): mixed
    {
        return '#unexist';
    }

    public final function testNull()
    {
        $null = Bags::null();
        $this->checkEmpty($null);
        $this->assertSame($null, $null->copy());
        $this->assertSame($null, Bags::null());
    }

    public final function testNullException()
    {
        $null = Bags::null();
        $this->expectException(UnmodifiableSetException::class);
        $null['something'] = true;
    }
}
