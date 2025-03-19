<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

use Time2Split\Help\Container\Entry;
use Time2Split\Help\Container\Set;
use Time2Split\Help\Container\Sets;
use Time2Split\Help\Tests\Resource\BackedIntEnum;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class SetOfBackedEnumTest extends SetOfEnumTest
{
    #[\Override]
    protected static function provideContainerOfEnumType($enumType): Set
    {
        return Sets::ofBackedEnum($enumType);
    }

    #[\Override]
    protected static function provideEntries(): array
    {
        return [
            new Entry(BackedIntEnum::a, true),
            new Entry(BackedIntEnum::b, true),
            new Entry(BackedIntEnum::c, true),
            new Entry(BackedIntEnum::d, true),
            new Entry(BackedIntEnum::e, true),
            new Entry(BackedIntEnum::f, true),
        ];
    }

    #[\Override]
    protected static function provideEnumType(): string
    {
        return BackedIntEnum::class;
    }

    // ========================================================================

    public final function testOfEnum()
    {
        $this->expectException(\InvalidArgumentException::class);
        Sets::ofBackedEnum(\UnitEnum::class);
    }
}
