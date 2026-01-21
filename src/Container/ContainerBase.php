<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Class\Clearable;
use Time2Split\Help\Container\Class\Copyable;
use Time2Split\Help\Container\Class\GetUnmodifiable;

/**
 * The base container functionalities.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 * @extends \Traversable<K,V>
 * @extends GetUnmodifiable<ContainerBase<K,V>>
 */
interface ContainerBase extends
    \Countable,
    \Traversable,
    Clearable,
    Copyable,
    GetUnmodifiable {}
