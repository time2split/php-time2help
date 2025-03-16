<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

use Time2Split\Help\Set;
use Time2Split\Help\Sets;
use Time2Split\Help\Tests\Container\BagSetTestTrait;

/**
 * @author Olivier Rodriguez (zuri)
 */
trait SetTestTrait
{
    use
        BagSetTestTrait,
        SetTestArrayValueTrait;

    protected function unmodifiableContainer($subject): Set
    {
        return Sets::unmodifiable($subject);
    }

    protected function containerEquals($a, $b): bool
    {
        return Sets::equals($a, $b);
    }

    protected function containerIncludedIn($a, $b): bool
    {
        return Sets::includedIn($a, $b);
    }

    // ========================================================================

    public final function testPutMoreCount(): void
    {
        [$item, $subject] = $this->provideOneItemContainer();
        $items = $this->provideThreeItems();

        $subject->setMore(...$items);
        $this->checkNotEmpty($subject, 4);
    }
}
