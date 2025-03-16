<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation of \ArrayAccess using an internal storage.
 * 
 * ```
 * function offsetExists(mixed $offset): bool
 * {
 *     return isset($this->storage[$offset]);
 * }
 * function offsetGet(mixed $offset): mixed
 * {
 *     return $this->storage[$offset];
 * }
 * function offsetSet(mixed $offset, mixed $value): void
 * {
 *     $this->storage[$offset] = $value;
 * }
 * function offsetUnset(mixed $offset): void
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
        return isset($this->storage[$offset]);
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
