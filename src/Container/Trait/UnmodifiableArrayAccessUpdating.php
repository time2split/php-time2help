<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Exception\UnmodifiableException;
use Time2Split\Help\Container\Class\ArrayAccessUpdating; //phpstan

/**
 * An implementation for an unmodifiable `ArrayAccessUpdating`.

 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @see \Time2Split\Help\Classes\GetUnmodifiable
 * @see \Time2Split\Help\Classes\IsUnmodifiable
 * @see \Time2Split\Help\Container\ArrayAccessUpdating
 * 
 * @template K
 * @template V
 * 
 * @phpstan-require-implements ArrayAccessUpdating<K,V>
 */
trait UnmodifiableArrayAccessUpdating
{
    /**
     * (`IsUnmodifiable`)
     * 
     * @throws UnmodifiableException
     */
    #[\Override]
    public function updateEntries(iterable ...$entries): static
    {
        throw new UnmodifiableException;
    }

    /**
     * (`IsUnmodifiable`)
     * 
     * @throws UnmodifiableException
     */
    #[\Override]
    public function unsetMore(mixed ...$keys): static
    {
        throw new UnmodifiableException;
    }

    /**
     * (`IsUnmodifiable`)
     * 
     * @throws UnmodifiableException
     */
    #[\Override]
    public function unsetFromList(iterable ...$listsOfKeys): static
    {
        throw new UnmodifiableException;
    }
}
