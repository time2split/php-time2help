<?php

declare(strict_types=1);

namespace Time2Split\Help\Iterator;

use Time2Split\Help\Cast\Cast;
use Time2Split\Help\Iterable\ParallelFlag;

/**
 * @internal
 * @package time2help\container\iterator
 * @author Olivier Rodriguez (zuri)
 * 
 * @template K
 * @template V
 * 
 * @extends AbstractIteratorOperation<K,V>
 */
abstract class AbstractParallelIteratorOperation extends AbstractIteratorOperation
{
    /**
     * @phpstan-param iterable<mixed,iterable<K,V>> $iterables
     */
    public function __construct(ParallelFlag $flags, iterable $iterables)
    {
        $iterator = new \MultipleIterator($flags->value);
        foreach ($iterables as $it)
            $iterator->attachIterator(Cast::iterableToIterator($it));

        parent::__construct($iterator);
    }
}
