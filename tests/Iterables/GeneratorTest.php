<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Iterables;

use Generator;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class GeneratorTest extends AbstractIteratorsTestClass
{
    protected const bool IS_REWINDABLE = false;

    protected const int NB_ENTRIES = 6;

    #[\Override]
    final protected static function provideIterable(): Generator
    {
        yield 'a';
        yield 'b';
        yield 'c';
        yield 'X' => 1;
        yield 'Y' => 2;
        yield 'Z' => 3;
    }
}
