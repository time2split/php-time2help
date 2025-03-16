<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

use Time2Split\Help\Container\Set;
use Time2Split\Help\Container\Sets;

/**
 * @author Olivier Rodriguez (zuri)
 */
final class SetToArrayKeysTest extends SetArrayKeysTest
{
    protected function provideContainer(): Set
    {
        return Sets::toArrayKeys(
            fn($e) => $e,
            fn($e) => $e,
        );
    }
}
