<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use IteratorAggregate;
use SplObjectStorage;
use Traversable;

/**
 * A container working like a \SplObjectStorage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template O
 * @template V
 * 
 * @implements ContainerAA<O,V,ObjectContainer<O,V>,O,V>
 * @implements ArrayAccessUpdating<O,V>
 * @implements ContainerPutMethods<O>
 * @implements IteratorAggregate<O,V>
 */
abstract class ObjectContainer
implements
    ContainerAA,
    ArrayAccessUpdating,
    ContainerPutMethods,
    FetchingClosed,
    IteratorAggregate
{
    /**
     * @use Trait\ArrayAccessPutKey<O>
     * @use Trait\ArrayAccessUpdating<O,V>
     * @use Trait\ArrayAccessWithStorage<O,V>
     * @use Trait\FetchingClosed<O,V, ObjectContainer<O,V>>
     * @use Trait\ToArrayToArrayContainer<O,V>
     * @use Trait\IteratorToArrayOfEntries<O,V>
     */
    use
        Trait\ArrayAccessPutKey,
        Trait\ArrayAccessUpdating,
        Trait\ArrayAccessWithStorage,
        Trait\CountableWithStorage,
        Trait\FetchingClosed,
        Trait\IteratorAggregateWithStorage,
        Trait\ToArrayToArrayContainer,
        Trait\IteratorToArrayOfEntries;

    protected SplObjectStorage $storage;

    public function __construct()
    {
        $this->storage = new \SplObjectStorage;
    }

    #[\Override]
    public function unmodifiable(): self
    {
        return ObjectContainers::unmodifiable($this);
    }

    #[\Override]
    public static function null(): self
    {
        return ObjectContainers::null();
    }

    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->storage[$offset] ?? null;
    }

    #[\Override]
    public function getIterator(): Traversable
    {
        foreach ($this->storage as $v) {
            yield $v => $this->storage[$v];
        }
    }

    private static function copySplObjectStorage(\SplObjectStorage $storage): \SplObjectStorage
    {
        $ret = new \SplObjectStorage();
        $ret->addAll($storage);
        return $ret;
    }

    #[\Override]
    public function copy(): static
    {
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

        $ca = $this->count();
        $cb = $other->count();

        if ($ca !== $cb)
            return false;

        $copy = $this->copySplObjectStorage($this->storage);
        $copy->removeAll($other->storage);
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

        $ca = $this->count();
        $cb = $other->count();

        if ($ca > $cb)
            return false;

        $copy = $this->copySplObjectStorage($this->storage);
        $copy->removeAll($other->storage);
        return 0 === $copy->count();
    }
}
