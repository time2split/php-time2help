<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\ArrayContainer;

use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\ArrayContainers;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Tests\Container\AbstractArrayAccessContainerTestClass;

/**
 * @author Olivier Rodriguez (zuri)
 */
class ToArrayKeysContainerTest extends AbstractArrayAccessContainerTestClass
{
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
    protected static function provideContainer(): ArrayContainer
    {
        return ArrayContainers::toArrayKeys(fn(array $k) => \implode(',', $k));
    }

    #[\Override]
    protected static function provideEntries(): array
    {
        return [
            new Entry(['a', 'x'], true),
            new Entry(['c', 'x'], true),
            new Entry(['d', 'x'], true),
            new Entry(['e', 'x'], true),
            new Entry(['f', 'x'], true),
            new Entry(['g', 'x'], true),
        ];
    }
}
