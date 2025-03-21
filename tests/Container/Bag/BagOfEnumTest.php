<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Bag;

use Time2Split\Help\Container\Bag;
use Time2Split\Help\Container\Bags;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Tests\Container\AbstractBagSetOfEnumTestClass;
use Time2Split\Help\Tests\Resource\AUnitEnum;

/**
 * @author Olivier Rodriguez (zuri)
 */
class BagOfEnumTest extends AbstractBagSetOfEnumTestClass
{
    public static function provideContainerOfEnumType($enumType): Bag
    {
        return Bags::ofEnum($enumType);
    }

    #[\Override]
    protected static function provideEntries(): array
    {
        return [
            new Entry(AUnitEnum::a, 1),
            new Entry(AUnitEnum::b, 1),
            new Entry(AUnitEnum::c, 1),
            new Entry(AUnitEnum::d, 1),
            new Entry(AUnitEnum::e, 1),
            new Entry(AUnitEnum::f, 1),
        ];
    }

    #[\Override]
    protected static function arrayValueIsAbsent(mixed $value): bool
    {
        return $value === 0;
    }

    #[\Override]
    protected static function arrayValueIsPresent(mixed $value): bool
    {
        return $value === 1;
    }
}
