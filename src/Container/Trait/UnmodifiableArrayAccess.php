<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Exception\UnmodifiableSetException;

trait UnmodifiableArrayAccess
{
    public final function offsetSet(mixed $offset, mixed $value): void
    {
        throw new UnmodifiableSetException();
    }
}
