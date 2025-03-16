<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Bag;

use Time2Split\Help\Bag;
use Time2Split\Help\Bags;
use Time2Split\Help\Tests\Container\BagSetTestTrait;

/**
 * @author Olivier Rodriguez (zuri)
 */
trait BagTestTrait
{
    use
        BagSetTestTrait,
        BagTestArrayValueTrait;

    protected function unmodifiableContainer($subject): Bag
    {
        return Bags::unmodifiable($subject);
    }

    protected function containerEquals($a, $b): bool
    {
        return Bags::equals($a, $b);
    }

    protected function containerIncludedIn($a, $b): bool
    {
        return Bags::includedIn($a, $b);
    }
}
