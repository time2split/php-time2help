<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Bag;

use Time2Split\Help\Container\Bag;
use Time2Split\Help\Container\Bags;

/**
 * @author Olivier Rodriguez (zuri)
 */
class BagArrayKeysTest extends AbstractBagTestClass
{
    #[\Override]
    protected static function provideContainer(): Bag
    {
        return Bags::arrayKeys();
    }
}
