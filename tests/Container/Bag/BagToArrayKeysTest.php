<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

use Time2Split\Help\Container\Bag;
use Time2Split\Help\Container\Bags;
use Time2Split\Help\Tests\Container\Bag\BagArrayKeysTest;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class BagToArrayKeysTest extends BagArrayKeysTest
{
    protected function provideContainer(): Bag
    {
        return Bags::toArrayKeys(
            fn($e) => $e,
            fn($e) => $e,
        );
    }
}
