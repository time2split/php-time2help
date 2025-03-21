<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Iterables;

use EmptyIterator;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class EmptyIteratorTest extends AbstractIteratorsTestClass
{
    protected const bool IS_REWINDABLE = true;

    protected const int NB_ENTRIES = 0;

    #[\Override]
    final protected static function provideIterable(): iterable
    {
        return new EmptyIterator;
    }
}
