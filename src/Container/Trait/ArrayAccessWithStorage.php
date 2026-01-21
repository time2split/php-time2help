<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use ArrayAccess;

/**
 * An implementation of \ArrayAccess using an internal storage.
 * 
 * The internal storage must be of type array|\ArrayAccess.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 */
trait ArrayAccessWithStorage
{
    /**
     * @param K $offset
     */
    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        if ($this->storage instanceof ArrayAccess)
            return $this->storage->offsetExists($offset);

        return \array_key_exists($offset, $this->storage);
    }

    /**
     * @param K $offset
     * @return V
     */
    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->storage[$offset];
    }

    /**
     * @param K $offset
     * @param V $value
     */
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null === $offset)
            $this->storage[] = $value;
        else
            $this->storage[$offset] = $value;
    }

    /**
     * @param K $offset
     */
    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        unset($this->storage[$offset]);
    }
}
