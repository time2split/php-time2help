<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use ArrayAccess;

/**
 * An implementation of \ArrayAccess using an internal storage.
 * 
 * The internal storage must be of type array|\ArrayAccess.
 * 
 * ```
 * public function offsetExists(mixed $offset): bool
 * {
 *     return null !== $this->offsetGet($offset);
 * }
 * public function offsetGet(mixed $offset): mixed
 * {
 *     return $this->storage[$offset];
 * }
 * public function offsetSet(mixed $offset, mixed $value): void
 * {
 *     $this->storage[$offset] = $value;
 * }
 * public function offsetUnset(mixed $offset): void
 * {
 *     unset($this->storage[$offset]);
 * }
 * ```
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 * @var array<K,V>|ArrayAccess<K,V> $storage The internal storage must be defined into the class.
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
