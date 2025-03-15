<?php

declare(strict_types=1);

namespace Time2Split\Help\Trait;

trait NullArrayAccess
{
    use UnmodifiableArrayAccess;

    public final function offsetGet(mixed $offset): mixed
    {
        return null;
    }

    public final function count(): int
    {
        return 0;
    }

    public final function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}
