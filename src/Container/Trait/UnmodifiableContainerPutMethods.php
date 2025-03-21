<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Exception\UnmodifiableException;

/**
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
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
