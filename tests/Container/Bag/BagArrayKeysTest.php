<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Bag;

use PHPUnit\Framework\TestCase;
use Time2Split\Help\Bag;
use Time2Split\Help\Bags;
use Time2Split\Help\Tests\Container\Bag\BagTestTrait;

/**
 * @author Olivier Rodriguez (zuri)
 */
class BagArrayKeysTest extends TestCase
{
    use BagTestTrait;

    #[\Override]
    protected function provideContainer(): Bag
    {
        return Bags::arrayKeys();
    }

    #[\Override]
    protected function provideOneItem(): mixed
    {
        return 'item';
    }

    #[\Override]
    protected function provideOneUnexistantItem(): mixed
    {
        return '#unexists';
    }

    #[\Override]
    protected function provideListsForThreeItems(): array
    {
        return [['a', 'b'], ['c', 'a']];
    }
}
