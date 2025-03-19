<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Exception\UnmodifiableException;

/**
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait UnmodifiableContainerPutMethods
{
    #[\Override]
    public function putMore(...$items): static
    {
        throw new UnmodifiableException;
    }

    #[\Override]
    public function putFromList(iterable ...$lists): static
    {
        throw new UnmodifiableException;
    }
}
