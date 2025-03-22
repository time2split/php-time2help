<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Countable;
use Time2Split\Help\Classes\Copyable;
use Time2Split\Help\Classes\GetNullInstance;
use Time2Split\Help\Classes\GetUnmodifiable;

/**
 * A base implementation for a container with an internal array storage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template T
 * @template I
 * 
 * @extends Copyable<I>
 * @extends GetNullInstance<I>
 * @extends GetUnmodifiable<I>
 */
interface ContainerBase extends
    Clearable,
    Countable,
    Copyable,
    GetNullInstance,
    GetUnmodifiable {}
