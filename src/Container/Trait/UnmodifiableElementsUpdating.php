<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Exception\UnmodifiableException;

/**
 * An implementation for an unmodifiable `OfElements`.

 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @see \Time2Split\Help\Classes\IsUnmodifiable
 * @see \Time2Split\Help\Container\Class\OfElements
 */
trait UnmodifiableElementsUpdating
{
    #[\Override]
    public function putMore(...$elements): static
    {
        throw new UnmodifiableException;
    }

    #[\Override]
    public function putFromList(iterable ...$listsOfElements): static
    {
        throw new UnmodifiableException;
    }
}
