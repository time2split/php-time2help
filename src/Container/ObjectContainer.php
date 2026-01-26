<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Class\ElementsUpdating;
use Time2Split\Help\Container\Class\IsUnmodifiable;

/**
 * A container working like a \SplObjectStorage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\php
 * 
 * @template O
 * @template V
 * 
 * @extends ContainerAA<O,V>
 * @extends ElementsUpdating<O>
 */
interface ObjectContainer
extends
    ContainerAA,
    ElementsUpdating
{
    /**
     * @return IsUnmodifiable&ObjectContainer<O,V>
     */
    #[\Override]
    public function unmodifiable(): ObjectContainer&IsUnmodifiable;
}
