<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

use Time2Split\Help\Container\Entry;
use Time2Split\Help\Container\Set;
use Time2Split\Help\Container\Sets;
use Time2Split\Help\Tests\Container\AbstractBagSetOfEnumTestClass;
use Time2Split\Help\Tests\Resource\AUnitEnum;

/**
 * @author Olivier Rodriguez (zuri)
 */
class SetOfEnumTest extends AbstractBagSetOfEnumTestClass
{
    protected static function provideContainerOfEnumType($enumType): Set
    {
        return Sets::ofEnum($enumType);
    }

    #[\Override]
    protected static function provideEntries(): array
    {
        return [
            new Entry(AUnitEnum::a, true),
            new Entry(AUnitEnum::b, true),
            new Entry(AUnitEnum::c, true),
            new Entry(AUnitEnum::d, true),
            new Entry(AUnitEnum::e, true),
            new Entry(AUnitEnum::f, true),
        ];
    }

    #[\Override]
    protected static function arrayValueIsAbsent(mixed $value): bool
    {
        return $value === false;
    }

    #[\Override]
    protected static function arrayValueIsPresent(mixed $value): bool
    {
        return $value === true;
    }
}
