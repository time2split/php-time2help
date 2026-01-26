<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\_internal;

use Time2Split\Help\Container\PathEdge;
use Time2Split\Help\Exception\UnmodifiableException;

/**
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 * 
 * @template T
 * @implements PathEdge<T>
 */
abstract class AbstractPathEdge implements PathEdge
{
    #[\Override]
    public function count(): int
    {
        return $this->getType()->count();
    }

    #[\Override]
    public function offsetGet(mixed $offset): bool
    {
        return $this->getType()->offsetGet($offset);
    }

    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        return $this->getType()->offsetExists($offset);
    }

    /**
     * @throws UnmodifiableException
     */
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new UnmodifiableException;
    }

    /**
     * @throws UnmodifiableException
     */
    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        throw new UnmodifiableException;
    }
}
