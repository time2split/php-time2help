<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Cast\Cast;
use Time2Split\Help\Container\Class\OfElements; // phpstan

/**
 * An implementation of `OfElements::elements()`.
 * 
 * ```
 * public function elements(): \Traversable
 * {
 *     return Cast::iterableToIterator($this->toListOfElements());
 * }
 * ```
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * @see Time2Split\Help\Container\Class\OfElements::toListOfElements()
 * 
 * @template T
 * 
 * @phpstan-require-implements OfElements<K,V>
 */
trait ListOfElementsToElements
{
    /**
     * @phpstan-return \Traversable<int,T>
     */
    #[\Override]
    public function elements(): \Traversable
    {
        return Cast::iterableToIterator($this->toListOfElements());
    }
}
