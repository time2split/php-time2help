<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\_internal;

use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\ContainerBase;
use Time2Split\Help\Container\ObjectContainer;
use Time2Split\Help\Container\ObjectContainers;
use Time2Split\Help\Container\Trait\ArrayAccessPutKey;
use Time2Split\Help\Container\Trait\ArrayAccessUpdating;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\CountableWithStorage;
use Time2Split\Help\Container\Trait\FetchingClosed;
use Time2Split\Help\Container\Trait\IteratorAggregateWithStorage;
use Time2Split\Help\Container\Trait\IteratorToArrayOfEntries;
use Time2Split\Help\Container\Trait\ToArrayToArrayContainer;

/**
 * A container working like a \SplObjectStorage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\php
 * 
 * @template O of object
 * @template V
 * 
 * @implements ObjectContainer<O,V>
 * @implements \IteratorAggregate<O,V>
 */
abstract class ObjectContainerImpl
implements
    ObjectContainer,
    \IteratorAggregate
{
    /**
     * @use ArrayAccessPutKey<O>
     * @use ArrayAccessUpdating<O,V>
     * @use ArrayAccessWithStorage<O,V>
     * @use FetchingClosed<O,V, ObjectContainer<O,V>>
     * @use IteratorAggregateWithStorage<O,V>
     * @use ToArrayToArrayContainer<O,V>
     * @use IteratorToArrayOfEntries<O,V>
     */
    use
        ArrayAccessPutKey,
        ArrayAccessUpdating,
        ArrayAccessWithStorage,
        CountableWithStorage,
        FetchingClosed,
        IteratorAggregateWithStorage,
        ToArrayToArrayContainer,
        IteratorToArrayOfEntries;

    /**
     * @var \SplObjectStorage<O,V> $storage
     */
    protected \SplObjectStorage $storage;

    public function __construct()
    {
        $this->storage = new \SplObjectStorage;
    }

    #[\Override]
    public function unmodifiable(): ObjectContainer&IsUnmodifiable
    {
        return ObjectContainers::unmodifiable($this);
    }

    /*
    #[\Override]
    public static function null(): self
    {
        return ObjectContainers::null();
    }
    //*/

    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->storage[$offset] ?? null;
    }

    #[\Override]
    public function getIterator(): \Traversable
    {
        foreach ($this->storage as $v) {
            yield $v => $this->storage[$v];
        }
    }

    /**
     * @param \SplObjectStorage<O,V> $storage
     * @return \SplObjectStorage<O,V>
     */
    private static function copySplObjectStorage(\SplObjectStorage $storage): \SplObjectStorage
    {
        /**
         * @var \SplObjectStorage<O,V>
         */
        $ret = new \SplObjectStorage();
        $ret->addAll($storage);
        return $ret;
    }

    #[\Override]
    public function copy(): static
    {
        /**
         * @var static<O,V>
         */
        $copy = new static();
        $copy->storage = self::copySplObjectStorage($this->storage);
        return $copy;
    }

    #[\Override]
    public function clear(): void
    {
        $this->storage = new \SplObjectStorage;
    }

    #[\Override]
    public function equals(
        ContainerBase $other,
    ): bool {
        if ($this === $other)
            return true;
        if (!($other instanceof ObjectContainer))
            return false;

        $ca = $this->count();
        $cb = $other->count();

        if ($ca !== $cb)
            return false;

        $copy = $this->copySplObjectStorage($this->storage);

        if ($other instanceof self)
            $copy->removeAll($other->storage);
        else foreach ($other as $obj => $data)
            unset($copy[$obj]);

        return 0 === $copy->count();
    }

    #[\Override]
    public function isIncludedIn(
        ContainerBase $other,
        bool $strictInclusion = false,
    ): bool {
        if ($strictInclusion)
            return $this->isStrictlyIncludedIn($other);
        if ($this === $other)
            return true;
        if (!($other instanceof ObjectContainer))
            return false;

        $ca = $this->count();
        $cb = $other->count();

        if ($ca > $cb)
            return false;

        $copy = $this->copySplObjectStorage($this->storage);

        if ($other instanceof self)
            $copy->removeAll($other->storage);
        else foreach ($other as $obj => $data)
            unset($copy[$obj]);

        return 0 === $copy->count();
    }
}
