<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use ArrayAccess;
use Traversable;

/**
 * The class is traversable, can be accessed like an array
 * and can be transformed into an array.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\interface
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
