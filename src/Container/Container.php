<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Classes\Copyable;
use Time2Split\Help\Classes\GetNullInstance;
use Time2Split\Help\Classes\GetUnmodifiable;
use Traversable;

/**
 * A base implementation for a container with an internal array storage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 * @extends GetNullInstance<K,V>
 * @extends GetUnmodifiable<K,V>
 * @extends ToArray<K,V>
 * @extends Traversable<K,V>
 */
interface Container extends
    Clearable,
    \Countable,
    Copyable,
    GetNullInstance,
    GetUnmodifiable,
    ToArray,
    Traversable {}
