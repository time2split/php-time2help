<?php

declare(strict_types=1);

namespace Time2Split\Help\Iterator;

use Time2Split\Help\Cast\Cast;
use Time2Split\Help\Iterable\ParallelFlag;

/**
 * @internal
 * @package time2help\container\iterator
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractParallelIteratorOperation extends AbstractIteratorOperation
{
    public function __construct(ParallelFlag $flags, iterable $iterables)
    {
        $iterator = new \MultipleIterator($flags->value);
        foreach ($iterables as $it)
            $iterator->attachIterator(Cast::iterableToIterator($it));

        parent::__construct($iterator);
    }
}
