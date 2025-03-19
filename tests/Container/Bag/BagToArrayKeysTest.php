<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Bag;

use Time2Split\Help\Container\Bag;
use Time2Split\Help\Container\Bags;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Tests\Container\Bag\AbstractBagTestClass;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class BagToArrayKeysTest extends AbstractBagTestClass
{
    #[\Override]
    protected static function provideContainer(): Bag
    {
        return Bags::toArrayKeys(
            fn(array $intList) => 'sum:' . \array_sum($intList)
        );
    }

    #[\Override]
    protected static function provideEntries(): array
    {
        return [
            new Entry([0, 0, 0], 1),
            new Entry([0, 0, 1], 1),
            new Entry([0, 1, 2], 1),
            new Entry([1, 2, 3], 1),
            new Entry([2, 3, 4], 1),
            new Entry([3, 4, 5], 1),
        ];
    }
}
