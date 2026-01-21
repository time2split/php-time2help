<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Class\ContainerPutMethods;
use Time2Split\Help\Container\Class\FetchingClosed;
use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\Class\ToArray;

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
 * @extends ContainerPutMethods<O>
 * @extends ToArray<int,Entry<O,V>>
 */
interface ObjectContainer
extends
    ContainerAA,
    ContainerPutMethods,
    FetchingClosed,
    ToArray
{
    /**
     * @return IsUnmodifiable&ObjectContainer<O,V>
     */
    #[\Override]
    public function unmodifiable(): ObjectContainer&IsUnmodifiable;
}
