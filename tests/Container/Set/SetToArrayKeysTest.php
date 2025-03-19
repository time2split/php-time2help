<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

use Time2Split\Help\Container\Entry;
use Time2Split\Help\Container\Set;
use Time2Split\Help\Container\Sets;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class SetToArrayKeysTest extends AbstractSetTestClass
{
    #[\Override]
    protected static function provideContainer(): Set
    {
        return Sets::toArrayKeys(
            fn(array $intList) => 'sum:' . \array_sum($intList)
        );
    }

    #[\Override]
    protected static function provideEntries(): array
    {
        return [
            new Entry([0, 0, 0], true),
            new Entry([0, 0, 1], true),
            new Entry([0, 1, 2], true),
            new Entry([1, 2, 3], true),
            new Entry([2, 3, 4], true),
            new Entry([3, 4, 5], true),
        ];
    }
}
