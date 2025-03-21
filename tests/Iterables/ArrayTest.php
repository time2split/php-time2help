<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Iterables;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class ArrayTest extends AbstractIteratorsTestClass
{
    protected const bool IS_REWINDABLE = true;

    protected const int NB_ENTRIES = 6;

    #[\Override]
    final protected static function provideIterable(): iterable
    {
        return [
            'a',
            'b',
            'c',
            'X' => 1,
            'Y' => 2,
            'Z' => 3,
        ];
    }
}
