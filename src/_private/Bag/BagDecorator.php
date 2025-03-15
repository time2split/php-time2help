<?php

declare(strict_types=1);

namespace Time2Split\Help\_private\Bag;

use Time2Split\Help\Bag;

/**
 * @internal
 * 
 * @template D
 * @template T
 * @extends BagDecorator<T>
 * @implements \IteratorAggregate<T>
 * @author Olivier Rodriguez (zuri)
 */
abstract class BagDecorator extends BaseBag implements \IteratorAggregate
{
    /**
     * @param Bag<D> $decorate
     */
    public function __construct(protected readonly Bag $decorate) {}

    public function offsetGet($offset): int
    {
        return $this->decorate->offsetGet($offset);
    }

    public function count(): int
    {
        return $this->decorate->count();
    }

    public function clear(): void
    {
        $this->decorate->clear();
    }

    public function offsetSet($offset,  $value): void
    {
        $this->decorate->offsetSet($offset, $value);
    }

    public function getIterator(): \Traversable
    {
        return $this->decorate;
    }
}
