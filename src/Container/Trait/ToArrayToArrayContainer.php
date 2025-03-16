<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ArrayContainer;

/**
 * An implementation of `ToArray::toArrayContainer`.
 * 
 * ```
 * return new ArrayContainer($this->toArray());
 * ```
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 */
trait ToArrayToArrayContainer
{
    /**
     * @inheritdoc
     */
    #[\Override]
    public function toArrayContainer(): ArrayContainer
    {
        return new ArrayContainer($this->toArray());
    }
}
