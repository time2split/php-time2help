<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\_internal;

use Time2Split\Help\Container\Bag;
use Time2Split\Help\Container\Bags;
use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\ContainerAA;
use Time2Split\Help\Container\Trait\ArrayAccessPutKey;
use Time2Split\Help\Container\Trait\ArrayAccessUpdating;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\ClearableWithStorage;
use Time2Split\Help\Container\Trait\CountableWithStorage;
use Time2Split\Help\Container\Trait\ElementsToListOfElements;
use Time2Split\Help\Iterables;
use Time2Split\Help\TriState;

/**
 * @author Olivier Rodriguez (zuri)
 * 
 * @template T
 * @implements Bag<T>
 * @implements \IteratorAggregate<T,int>
 */
abstract class BagWithStorage
implements
    Bag,
    \IteratorAggregate
{
    /**
     * @use ArrayAccessPutKey<T>
     * @use ArrayAccessUpdating<T,int>
     * @use ArrayAccessWithStorage<T,int>
     * @use ElementsToListOfElements<T>
     */
    use
        ArrayAccessPutKey,
        ArrayAccessUpdating,
        ArrayAccessWithStorage,
        ClearableWithStorage,
        CountableWithStorage,
        ElementsToListOfElements;

    private int $count = 0;

    /**
     * @param ContainerAA<T,int> $storage
     */
    public function __construct(
        protected ContainerAA $storage
    ) {
        foreach ($storage as $nb) {
            /* @phpstan-ignore staticMethod.resultUnused */
            self::checkType($nb);
            $this->count += $nb;
        }
    }

    private static function checkType(int $nb): void {}

    #[\Override]
    public function copy(): static
    {
        return new static($this->storage->copy());
    }

    /*
    #[\Override]
    public static function null(): self
    {
        return Bags::null();
    }
    //*/

    /**
     * @return IsUnmodifiable&Bag<T>
     */
    #[\Override]
    public function unmodifiable(): Bag&IsUnmodifiable
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
        $this->storage->clear();
        $this->count = 0;
    }

    #[\Override]
    public function offsetGet(mixed $item): int
    {
        return $this->storage[$item] ?? 0;
    }

    #[\Override]
    public function getIterator(): \Traversable
    {
        return $this->traverseDuplicates($this->storage);
    }

    /**
     * @param iterable<T,int> $iterable
     * @return \Traversable<T>
     */
    protected function traverseDuplicates(iterable $iterable): \Traversable
    {
        foreach ($iterable as $item => $nb) {
            for ($i = 0; $i < $nb; $i++)
                yield $item => 1;
        }
    }

    #[\Override]
    public function elements(): \Traversable
    {
        yield from Iterables::keys($this);
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
        Bag $inside,
        TriState $strictInclusion = TriState::Maybe
    ): bool {
        return Bags::isIncludedIn($this, $inside, $strictInclusion);
    }
}
