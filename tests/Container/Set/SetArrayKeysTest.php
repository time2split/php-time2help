<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

use PHPUnit\Framework\TestCase;
use Time2Split\Help\Container\Set;
use Time2Split\Help\Container\Sets;

/**
 * @author Olivier Rodriguez (zuri)
 */
class SetArrayKeysTest extends TestCase
{
    use SetTestTrait;

    #[\Override]
    protected function provideContainer(): Set
    {
        return Sets::arrayKeys();
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
