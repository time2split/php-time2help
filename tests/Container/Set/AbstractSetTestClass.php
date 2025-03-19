<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

use Time2Split\Help\Tests\Container\AbstractBagSetTestClass;

/**
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractSetTestClass extends AbstractBagSetTestClass
{
    #[\Override]
    protected static function provideEntries(): array
    {
        return [
            ['a' => true],
            ['c' => true],
            ['d' => true],
            ['e' => true],
            ['f' => true],
            ['g' => true],
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
