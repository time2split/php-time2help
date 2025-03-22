<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\ArrayContainer;

use Closure;
use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\ArrayContainers;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Tests\Container\AbstractArrayAccessContainerTestClass;

/**
 * @author Olivier Rodriguez (zuri)
 */
class ArrayContainerTest extends AbstractArrayAccessContainerTestClass
{
    #[\Override]
    protected static function entriesEqualClosure_putMethodTest(bool $strict = false): Closure
    {
        $eq = Entry::equalsClosure($strict);
        return fn(
            Entry $expect,
            Entry $subject
        ) =>  $eq($expect->flip()->setkey(0), $subject->setKey(0));
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
    protected static function provideContainer(): ArrayContainer
    {
        return ArrayContainers::create();
    }
}
