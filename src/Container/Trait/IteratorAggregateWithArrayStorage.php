<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use ArrayIterator;
use Iterator;
use Traversable;

/**
 * An implementation of \IteratorAggregate returning the internal array storage.
 * 
 * ```
 * public function getIterator(): \Traversable
 * {
 *     return new \ArrayIterator($this->storage);
 * }
 * ```
 * 
 * (It must have a property: `array $storage`)
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 * 
 * @phpstan-property array<K,V> $storage
 * @phpstan-require-implements \IteratorAggregate<K,V>
 */
trait IteratorAggregateWithArrayStorage
{
    /**
     * @return Iterator<K,V>
     */
    #[\Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->storage);
    }
}
