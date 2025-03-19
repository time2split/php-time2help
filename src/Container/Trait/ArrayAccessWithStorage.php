<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

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
 * @var array|ArrayAccess $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait ArrayAccessWithStorage
{
    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        return $this->storage->offsetExists($offset);
    }

    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->storage[$offset];
    }

    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->storage[$offset] = $value;
    }

    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        unset($this->storage[$offset]);
    }
}
