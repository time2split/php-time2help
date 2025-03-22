<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\_internal;

use IteratorAggregate;
use Time2Split\Help\Container\ContainerAA;
use Time2Split\Help\Container\ContainerBase;
use Time2Split\Help\Container\Set;
use Time2Split\Help\Container\Sets;
use Time2Split\Help\Container\Trait\ArrayAccessPutKey;
use Time2Split\Help\Container\Trait\ArrayAccessUpdating;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\ClearableWithStorage;
use Time2Split\Help\Container\Trait\CountableWithStorage;
use Time2Split\Help\Container\Trait\FetchingClosed;
use Time2Split\Help\Container\Trait\IteratorAggregateWithStorage;
use Time2Split\Help\Container\Trait\IteratorToArray;
use Time2Split\Help\Container\Trait\IteratorToArrayContainer;

/**
 * @author Olivier Rodriguez (zuri)
 * 
 * @template T
 * @implements Set<T>
 * @implements IteratorAggregate<int,T>
 */
abstract class SetWithStorage
implements
    Set,
    IteratorAggregate
{
    /**
     * @use ArrayAccessPutKey<T>
     * @use ArrayAccessUpdating<T,bool>
     * @use ArrayAccessWithStorage<T,bool>
     * @use FetchingClosed<bool,T,Set<T>>
     * @use IteratorAggregateWithStorage<int,T>
     * @use IteratorToArray<int,T>
     * @use IteratorToArrayContainer<int,T>
     */
    use
        ArrayAccessPutKey,
        ArrayAccessUpdating,
        ArrayAccessWithStorage,
        ClearableWithStorage,
        CountableWithStorage,
        FetchingClosed,
        IteratorAggregateWithStorage,
        IteratorToArray,
        IteratorToArrayContainer;

    public function __construct(
        protected ContainerAA $storage
    ) {
        $this->storage = $storage;
    }

    /**
     * @internal
     * */
    public function getStorage(): ContainerAA
    {
        return $this->storage;
    }

    #[\Override]
    public function copy(): static
    {
        return new static($this->storage->copy());
    }

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
        ContainerBase $other,
    ): bool {
        return Sets::equals($this, $other);
    }

    #[\Override]
    public function isIncludedIn(
        ContainerBase $other,
        bool $strictInclusion = false,
    ): bool {
        if ($strictInclusion)
            return $this->isStrictlyIncludedIn($other);
        else
            return Sets::isIncludedIn($this, $other);
    }
}
