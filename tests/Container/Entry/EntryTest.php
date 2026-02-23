<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Path;

use Time2Split\Help\Container\Entry;
use Time2Split\Help\Tests\Container\AbstractArrayAccessContainerTestClass;

final class EntryTest extends AbstractArrayAccessContainerTestClass
{
    protected const MIN_NB_ENTRIES = 2;

    protected static function provideEntries(): array
    {
        return [
            'key',
            'value',
        ];
    }

    #[\Override]
    protected static function arrayValueIsAbsent(mixed $value): bool
    {
        return null === $value;
    }

    #[\Override]
    protected static function arrayValueIsPresent(mixed $value): bool
    {
        return null !== $value;
    }

    #[\Override]
    protected static function provideContainer(): Entry
    {
        return new Entry(...self::provideEntries());
    }

    #[\Override]
    protected static function provideContainerWithSubEntries(int $offset = 0, ?int $length = null): Entry
    {
        return new Entry(...self::provideEntries());
    }
}
