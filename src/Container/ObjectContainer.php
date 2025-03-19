<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Trait\ArrayAccessUpdateMethods;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\IteratorAggregateWithStorage;
use Time2Split\Help\Container\Trait\IteratorToArrayOfEntries;
use Traversable;

/**
 * A container working like a \SplObjectStorage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
abstract class ObjectContainer
extends ContainerWithStorage
implements ArrayAccessContainer
{
    use
        ArrayAccessUpdateMethods,
        ArrayAccessWithStorage,
        IteratorAggregateWithStorage,
        IteratorToArrayOfEntries;

    public function __construct()
    {
        parent::__construct(new \SplObjectStorage);
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
}
