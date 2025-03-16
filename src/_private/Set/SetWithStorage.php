<?php

declare(strict_types=1);

namespace Time2Split\Help\_private\Set;

use InvalidArgumentException;
use Time2Split\Help\Tests\Container\Copyable;

/**
 * @internal
 * @author Olivier Rodriguez (zuri)
 */
abstract class SetWithStorage extends BaseSet implements \IteratorAggregate
{
    /**
     * @var bool[]|(\Traversable<mixed,bool>&\Countable) $storage
     */
    protected array|(\Traversable&\Countable) $storage;

    protected $copyStorage;

    public function __construct(
        mixed $storage = [],
        ?callable $copyStorage = null,
    ) {
        $this->storage = $storage;
        $this->copyStorage = $copyStorage;

        if (null == $copyStorage) {

            if (\is_array($storage))
                $this->copyStorage = fn(array $storage) => $storage;
            elseif (!($this->storage instanceof Copyable))
                throw new InvalidArgumentException("The storage must be an instance of Copyable");
        }
    }

    public function offsetGet(mixed $offset): bool
    {
        return $this->storage[$offset] ?? false;
    }

    public function count(): int
    {
        return \count($this->storage);
    }

    protected function storageCopy(): array|(\Traversable&\Countable)
    {
        if (null !== $this->copyStorage)
            return ($this->copyStorage)($this->storage);

        assert($this->storage instanceof Copyable);
        return $this->storage->copy();
    }

    public function copy(): static
    {
        return new static($this->storageCopy(), $this->copyStorage);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!\is_bool($value))
            throw new \InvalidArgumentException('Must be a boolean');

        if ($value)
            $this->storage[$offset] = true;
        else
            unset($this->storage[$offset]);
    }

    public function getIterator(): \Traversable
    {
        if (\is_array($this->storage))
            return new \ArrayIterator(\array_keys($this->storage));
        else
            return $this->storage;
    }
}
