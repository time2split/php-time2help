<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation of \IteratorAggregate returning the internal storage.
 * 
 * ```
 * function getIterator(): \Traversable
 * {
 *     return $this->storage;
 * }
 * ```
 * 
 * @var \Traversable $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait IteratorAggregateWithStorage
{
    #[\Override]
    public function getIterator(): \Traversable
    {
        return $this->storage;
    }
}
