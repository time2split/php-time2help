<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\Class\OfElements; //phpstan

/**
 * An implementation of `OfElements::toListOfElements()`.
 * 
 * ```
 * public function toListOfElements(): array
 * {
 *     return \iterator_to_array($this->elements());
 * }
 * ```
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * @see Time2Split\Help\Container\Class\OfElements::toListOfElements()
 * 
 * @template T
 * 
 * @phpstan-require-implements OfElements<T>
 */
trait ElementsToListOfElements
{
    /**
     * @return array<int,T>
     */
    #[\Override]
    public function toListOfElements(): array
    {
        return \iterator_to_array($this->elements());
    }
}
