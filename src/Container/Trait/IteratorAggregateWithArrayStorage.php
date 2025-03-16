<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation of \IteratorAggregate returning the internal array storage.
 * 
 * @var array $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait IteratorAggregateWithArrayStorage
{
    #[\Override]
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->storage);
    }
}
