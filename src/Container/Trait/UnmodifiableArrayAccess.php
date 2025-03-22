<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Exception\UnmodifiableException;

/**
 * An implementation for an unmodifiable `\ArrayAccess`.
 * 
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 * 
 * @see \Time2Split\Help\Classes\GetUnmodifiable
 * @see \Time2Split\Help\Classes\IsUnmodifiable
 */
trait UnmodifiableArrayAccess
{
    #[\Override]
    public final function offsetSet(mixed $offset, mixed $value): void
    {
        throw new UnmodifiableException();
    }

    #[\Override]
    public final function offsetUnset(mixed $offset): void
    {
        throw new UnmodifiableException();
    }
}
