<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\ArrayContainer;

use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\ArrayContainers;
use Time2Split\Help\Tests\Container\AbstractArrayAccessContainerTestClass;

/**
 * @author Olivier Rodriguez (zuri)
 */
class ArrayContainerTest extends AbstractArrayAccessContainerTestClass
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
        return ArrayContainers::create();
    }
}
