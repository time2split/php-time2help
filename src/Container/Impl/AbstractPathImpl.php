<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Impl;

use Time2Split\Help\Container\PathEdge; //phpstan
use Time2Split\Help\TriState;

/**
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\path
 * 
 * @template T
 */
abstract class AbstractPathImpl
{
    /**
     * @phpstan-param iterable<PathEdge<T>> $edges
     */
    abstract protected function __construct(
        TriState $rooted,
        TriState $leafed,
        iterable $edges,
        ?bool $canonicalized = null,
    );
}
