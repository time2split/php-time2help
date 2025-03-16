<?php

declare(strict_types=1);

namespace Time2Split\Help\_private\Set;

use Time2Split\Help\_private\Trait\BagSetWithStorage;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Set;

/**
 * @internal
 * @author Olivier Rodriguez (zuri)
 */
abstract class SetWithStorage
extends BagSetWithStorage
implements Set
{
    use ArrayAccessWithStorage;

    #[\Override]
    public function offsetGet(mixed $offset): bool
    {
        return $this->storage[$offset] ?? false;
    }

    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!\is_bool($value))
            throw new \InvalidArgumentException('Must be a boolean');

        if ($value)
            $this->storage[$offset] = $value;
        else
            unset($this->storage[$offset]);
    }
}
