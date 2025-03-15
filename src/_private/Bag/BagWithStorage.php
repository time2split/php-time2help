<?php

declare(strict_types=1);

namespace Time2Split\Help\_private\Bag;

/**
 * @internal
 */
abstract class BagWithStorage extends BaseBag implements \IteratorAggregate
{
    /**
     * @var int[]|(\Traversable<mixed,int>&\Countable) $storage
     */
    protected array|(\Traversable&\Countable) $storage;

    private $count = 0;

    public function __construct(mixed $storage)
    {
        $this->storage = $storage;
    }

    public function offsetGet(mixed $offset): int
    {
        return $this->storage[$offset] ?? 0;
    }

    public final function count(): int
    {
        return $this->count;
    }

    public function offsetset(mixed $offset, mixed $value): void
    {
        if (!\is_bool($value) && !\is_int($value))
            throw new \InvalidArgumentException('Must be a boolean/integer');

        if (\is_bool($value))
            $nb = $value ? 1 : -1;
        else
            $nb = $value;

        if ($nb > 0)
            $this->put($offset, $nb);
        else
            $this->drop($offset, -$nb);
    }

    private function put(mixed $item, int $nb): void
    {
        $this->count += $nb;

        if (isset($this->storage[$item]))
            $this->storage[$item] += $nb;
        else
            $this->storage[$item] = $nb;
    }

    private function drop(mixed $item, int $nb): void
    {
        $nbset = $this[$item];

        if ($nbset === 0)
            return;
        if ($nb >= $nbset) {
            unset($this->storage[$item]);
            $this->count -= $nbset;
        } else {
            $this->storage[$item] -= $nb;
            $this->count -= $nb;
        }
    }

    public function clear(): void
    {
        $this->count = 0;
    }

    protected function getStorageIterator(): iterable
    {
        return $this->storage;
    }

    public function getIterator(): \Traversable
    {
        $p = 0;
        foreach ($this->getStorageIterator() as $item => $nb)
            for ($i = 0; $i++ < $nb;)
                yield $p++ => $item;
    }
}
