<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\_internal;

use Time2Split\Help\Container\ContainerWithContainerStorage;
use Time2Split\Help\Container\Set;
use Time2Split\Help\Container\Sets;
use Time2Split\Help\Container\Trait\ArrayAccessAssignItems;
use Time2Split\Help\Container\Trait\ArrayAccessUpdating;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\FetchingClosed;

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
        ArrayAccessUpdating,
        ArrayAccessWithStorage,
        FetchingClosed;

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

    #[\Override]
    public function equals(
        SetWithStorage $other,
    ): bool {
        return Sets::equals($this, $other);
    }

    #[\Override]
    public function isIncludedIn(
        SetWithStorage $other,
        bool $strictInclusion = false,
    ): bool {
        if ($strictInclusion)
            return $this->isStrictlyIncludedIn($other);
        else
            return Sets::isIncludedIn($this, $other);
    }
}
