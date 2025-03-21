<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\_internal;

use Time2Split\Help\Container\Bag;
use Time2Split\Help\Container\Bags;
use Time2Split\Help\Container\Container;
use Time2Split\Help\Container\ContainerWithContainerStorage;
use Time2Split\Help\Container\Trait\ArrayAccessAssignItems;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\FetchingClosed;
use Traversable;

/**
 * @internal
 * @author Olivier Rodriguez (zuri)
 */
abstract class BagWithStorage
extends ContainerWithContainerStorage
implements Bag
{
    use
        ArrayAccessAssignItems,
        ArrayAccessWithStorage,
        FetchingClosed;

    private $count = 0;

    public function __construct(Container $storage)
    {
        parent::__construct($storage);

        foreach ($storage as $nb) {
            self::checkType($nb);
            $this->count += $nb;
        }
    }

    private static function checkType(int $nb) {}

    #[\Override]
    public function copy(): static
    {
        return new static($this->storage->copy());
    }

    #[\Override]
    public static function null(): self
    {
        return Bags::null();
    }

    #[\Override]
    public function unmodifiable(): self
    {
        return Bags::unmodifiable($this);
    }

    #[\Override]
    public function count(): int
    {
        return $this->count;
    }

    #[\Override]
    public function clear(): void
    {
        parent::clear();
        $this->count = 0;
    }

    #[\Override]
    public function offsetGet(mixed $item): int
    {
        return $this->storage[$item] ?? 0;
    }

    #[\Override]
    public function getIterator(): Traversable
    {
        return $this->traverseDuplicates(parent::getIterator());
    }

    protected function traverseDuplicates(iterable $iterable): \Traversable
    {
        foreach ($iterable as $item => $nb) {
            for ($i = 0; $i < $nb; $i++)
                yield $item => 1;
        }
    }

    #[\Override]
    public function offsetSet(mixed $item, mixed $value): void
    {
        if (\is_bool($value))
            $nb = $value ? 1 : -1;
        elseif (\is_integer($value))
            $nb = $value;
        else
            throw new \InvalidArgumentException('Must be a boolean/integer');

        $nb > 0 ?
            $this->put($item, $nb) :
            $this->drop($item, -$nb);
    }

    #[\Override]
    public function offsetUnset(mixed $item): void
    {
        $this->drop($item, 1);
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
        $nbset = $this->storage[$item];

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

    #[\Override]
    public function equals(
        Bag $other,
    ): bool {
        return Bags::equals($this, $other);
    }

    #[\Override]
    public function isIncludedIn(
        Bag $other,
        bool $strictInclusion = false,
    ): bool {
        if ($strictInclusion)
            return $this->isStrictlyIncludedIn($other);
        else
            return Bags::isIncludedIn($this, $other);
    }
}
