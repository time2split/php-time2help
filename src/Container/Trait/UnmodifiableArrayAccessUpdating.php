<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Exception\UnmodifiableException;

/**
 * An implementation for an unmodifiable `ArrayAccessUpdating`.

 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @see \Time2Split\Help\Classes\GetUnmodifiable
 * @see \Time2Split\Help\Classes\IsUnmodifiable
 * @see \Time2Split\Help\Container\ArrayAccessUpdating
 */
trait UnmodifiableArrayAccessUpdating
{
    #[\Override]
    public function updateEntries(iterable ...$entries): static
    {
        throw new UnmodifiableException;
    }

    #[\Override]
    public function unsetMore(mixed ...$keys): static
    {
        throw new UnmodifiableException;
    }

    #[\Override]
    public function unsetFromList(iterable ...$listsOfKeys): static
    {
        throw new UnmodifiableException;
    }
}
