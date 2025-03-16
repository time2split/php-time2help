<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Bag;


/**
 * @author Olivier Rodriguez (zuri)
 */
trait BagTestArrayValueTrait
{
    #[\Override]
    protected function arrayValueIsAbsent(mixed $value): bool
    {
        return $value === 0;
    }

    #[\Override]
    protected function arrayValueIsPresent(mixed $value): bool
    {
        return $value > 0;
    }
}
