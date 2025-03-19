<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Bag;

use Time2Split\Help\Container\Entry;
use Time2Split\Help\Container\Bag;
use Time2Split\Help\Container\Bags;
use Time2Split\Help\Tests\Resource\BackedIntEnum;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class BagOfBackedEnumTest extends BagOfEnumTest
{
    #[\Override]
    public static function provideContainerOfEnumType($enumType): Bag
    {
        return Bags::ofBackedEnum($enumType);
    }

    #[\Override]
    protected static function provideEntries(): array
    {
        return [
            new Entry(BackedIntEnum::a, 1),
            new Entry(BackedIntEnum::b, 1),
            new Entry(BackedIntEnum::c, 1),
            new Entry(BackedIntEnum::d, 1),
            new Entry(BackedIntEnum::e, 1),
            new Entry(BackedIntEnum::f, 1),
        ];
    }

    #[\Override]
    public static function provideEnumType(): string
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
