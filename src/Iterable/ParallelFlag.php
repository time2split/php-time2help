<?php

namespace Time2Split\Help\Iterable;

use MultipleIterator;

/**
 * Flag used for Iterables::parallel().
 * 
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 * @see \Time2Split\Help\Iterables::parallelWithFlags()
 */
enum ParallelFlag: int
{
    /**
     * Do not require all sub iterators to be valid in iteration.
     */
    case NEED_ANY = MultipleIterator::MIT_NEED_ANY;

    /**
     *  Require all sub iterators to be valid in iteration.
     */
    case NEED_ALL = MultipleIterator::MIT_NEED_ALL;
}
