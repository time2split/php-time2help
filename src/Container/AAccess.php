<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use ArrayAccess;
use Traversable;

/**
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 * 
 * @extends ArrayAccess<K,V>
 * @extends ToArray<K,V>
 * @extends Traversable<K,V>
 */
interface AAccess
extends
    ArrayAccess,
    ToArray,
    Traversable {}
