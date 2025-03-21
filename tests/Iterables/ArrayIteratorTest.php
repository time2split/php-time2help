<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Iterables;

use ArrayIterator;
use Iterator;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class ArrayIteratorTest extends AbstractIteratorsTestClass
{
    protected const bool IS_REWINDABLE = true;

    protected const int NB_ENTRIES = 6;

    #[\Override]
    final protected static function provideIterable(): Iterator
    {
        return new ArrayIterator([
            'a',
            'b',
            'c',
            1,
            2,
            3,
        ]);
    }
}
