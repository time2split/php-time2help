<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Iterables;

use Time2Split\Help\Container\ArrayContainers;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class ArrayContainerTest extends AbstractIteratorsTestClass
{
    protected const bool IS_REWINDABLE = true;

    protected const int NB_ENTRIES = 6;

    #[\Override]
    final protected static function provideIterable(): iterable
    {
        return ArrayContainers::create([
            'a',
            'b',
            'c',
            'X' => 1,
            'Y' => 2,
            'Z' => 3,
        ]);
    }
}
