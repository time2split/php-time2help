<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

/**
 * @author Olivier Rodriguez (zuri)
 */
trait SetTestArrayValueTrait
{
    #[\Override]
    protected function arrayValueIsAbsent(mixed $value): bool
    {
        return $value === false;
    }

    #[\Override]
    protected function arrayValueIsPresent(mixed $value): bool
    {
        return $value === true;
    }
}
