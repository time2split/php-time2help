<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use ArrayIterator;
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
 * @var array $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 */
trait IteratorAggregateWithArrayStorage
{
    /**
     * @return Traversable<K,V>
     */
    #[\Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->storage);
    }
}
