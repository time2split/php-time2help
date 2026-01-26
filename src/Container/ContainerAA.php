<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Class\ArrayAccessUpdating;
use Time2Split\Help\Container\Class\IsUnmodifiable;

/**
 * A container accessible like an array.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 * @extends \ArrayAccess<K,V>
 * @extends ArrayAccessUpdating<K,V>
 * @extends ContainerBase<K,V>
 */
interface ContainerAA
extends
    \ArrayAccess,
    ArrayAccessUpdating,
    ContainerBase
{
    /**
     * @return ContainerAA<K,V>&IsUnmodifiable
     */
    #[\Override]
    public function unmodifiable(): ContainerAA&IsUnmodifiable;
}
