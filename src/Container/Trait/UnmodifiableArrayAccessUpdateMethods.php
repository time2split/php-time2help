<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Exception\UnmodifiableException;

/**
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait UnmodifiableArrayAccessUpdateMethods
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
