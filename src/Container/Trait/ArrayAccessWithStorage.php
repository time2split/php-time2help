<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use ArrayAccess;

/**
 * An implementation of \ArrayAccess using an internal storage.
 * 
 * (It must have a property: `array|\ArrayAccess $storage`)
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 * 
 * @phpstan-require-implements ArrayAccess<K,V>
 * @phpstan-property array<K,V>|ArrayAccess<K,V> $storage
 */
trait ArrayAccessWithStorage
{
    /**
     * @phpstan-param K $offset
     */
    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        /** @phpstan-ignore instanceof.alwaysFalse, instanceof.alwaysTrue */
        if ($this->storage instanceof ArrayAccess)
            return $this->storage->offsetExists($offset);

        return \array_key_exists($offset, $this->storage);
    }

    /**
     * @phpstan-param K $offset
     * @phpstan-return V
     */
    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->storage[$offset] ?? null;
    }

    /**
     * @phpstan-param ?K $offset
     * @phpstan-param V $value
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
     * @phpstan-param K $offset
     */
    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        unset($this->storage[$offset]);
    }
}
