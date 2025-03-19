<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\_internal;

use Time2Split\Help\Container\ContainerWithContainerStorage;
use Time2Split\Help\Container\Set;
use Time2Split\Help\Container\Sets;
use Time2Split\Help\Container\Trait\ArrayAccessAssignItems;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;

/**
 * @internal
 * @author Olivier Rodriguez (zuri)
 */
abstract class SetWithStorage
extends ContainerWithContainerStorage
implements Set
{
    use
        ArrayAccessAssignItems,
        ArrayAccessWithStorage;

    #[\Override]
    public function offsetGet(mixed $offset): bool
    {
        return $this->storage[$offset] ?? false;
    }
    #[\Override]
    public static function null(): self
    {
        return Sets::null();
    }
    #[\Override]
    public function unmodifiable(): self
    {
        return Sets::unmodifiable($this);
    }
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!\is_bool($value))
            throw new \InvalidArgumentException('Must be a boolean, have: ' . \print_r($value, true));

        if ($value)
            $this->storage[$offset] = $value;
        else
            unset($this->storage[$offset]);
    }
}
