<?php

namespace Time2Split\Help\Container\Trait;

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
 */
trait ElementsToListOfElements
{
    /**
     * @return T[]
     */
    #[\Override]
    public function toListOfElements(): array
    {
        return \iterator_to_array($this->elements());
    }
}
