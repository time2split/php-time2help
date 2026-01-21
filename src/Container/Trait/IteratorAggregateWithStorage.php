<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Cast\Cast;
use Traversable;

/**
 * An implementation of \IteratorAggregate returning the internal storage.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 */
trait IteratorAggregateWithStorage
{
    /**
     * @return Traversable<K,V>
     */
    #[\Override]
    public function getIterator(): Traversable
    {
        return Cast::iterableToIterator($this->storage);
    }
}
