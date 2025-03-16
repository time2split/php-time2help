<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

use Time2Split\Help\Container\Set;
use Time2Split\Help\Container\Sets;
use Time2Split\Help\Tests\Resource\BackedIntEnum;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class SetOfBackedEnumTest extends SetOfEnumTest
{
    #[\Override]
    public function provideContainerOfEnumType($enumType): Set
    {
        return Sets::ofBackedEnum($enumType);
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
        Sets::ofBackedEnum(\UnitEnum::class);
    }
}
