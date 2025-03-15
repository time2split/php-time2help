<?php

declare(strict_types=1);

namespace Time2Split\Help\Trait;

trait IteratorToArray
{
    public final function toArray(): array
    {
        return \iterator_to_array($this);
    }
}
