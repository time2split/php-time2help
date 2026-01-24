<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\ObjectContainer;

use Time2Split\Help\Container\Entry;
use Time2Split\Help\Container\ObjectContainer;
use Time2Split\Help\Container\ObjectContainers;
use Time2Split\Help\Iterables;
use Time2Split\Help\Tests\Container\AbstractArrayAccessContainerTestClass;

/**
 * @author Olivier Rodriguez (zuri)
 */
class ObjectContainerTest extends AbstractArrayAccessContainerTestClass
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
    protected static function provideContainer(): ObjectContainer
    {
        return ObjectContainers::create();
    }

    #[\Override]
    protected static function provideEntries(): array
    {
        // Must stay the same object references
        static $data =
        [
            new Entry((object)['a' => 1], 10),
            new Entry((object)['b' => 2], 20),
            new Entry((object)['c' => 3], 30),
            new Entry((object)['d' => 4], 40),
            new Entry((object)['e' => 5], 50),
            new Entry((object)['f' => 6], 60),
        ];
        return $data;
    }

    #[\Override]
    protected static function putMethodTest_makeEntries(iterable $entries): iterable
    {
        return Iterables::keys($entries);
    }
}
