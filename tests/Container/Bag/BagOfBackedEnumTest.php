<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Bag;

use Time2Split\Help\Bag;
use Time2Split\Help\Bags;
use Time2Split\Help\Tests\Resource\BackedIntEnum;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class BagOfBackedEnumTest extends BagOfEnumTest
{
    #[\Override]
    public function provideContainerOfEnumType($enumType): Bag
    {
        return Bags::ofBackedEnum($enumType);
    }

    #[\Override]
    public function provideEnumType(): string
    {
        return BackedIntEnum::class;
    }

    // ========================================================================

    public final function testOfEnum()
    {
        $this->expectException(\InvalidArgumentException::class);
        Bags::ofBackedEnum(\UnitEnum::class);
    }
}
