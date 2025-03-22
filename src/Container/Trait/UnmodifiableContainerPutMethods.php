<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Exception\UnmodifiableException;

/**
 * An implementation for an unmodifiable implementation of `ContainerPutMethods`.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @see \Time2Split\Help\Classes\GetUnmodifiable
 * @see \Time2Split\Help\Classes\IsUnmodifiable
 * @see \Time2Split\Help\Container\ContainerPutMethods
 * 
 * @template V
 */
trait UnmodifiableContainerPutMethods
{
    /**
     * @param V ...$items
     */
    #[\Override]
    public function putMore(...$items): static
    {
        throw new UnmodifiableException;
    }

    /**
     * @param iterable<int,V> ...$lists
     */
    #[\Override]
    public function putFromList(iterable ...$lists): static
    {
        throw new UnmodifiableException;
    }
}
