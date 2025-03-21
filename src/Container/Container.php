<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Classes\Copyable;
use Time2Split\Help\Classes\GetNullInstance;
use Time2Split\Help\Classes\GetUnmodifiable;

interface Container extends
    Clearable,
    \Countable,
    Copyable,
    GetNullInstance,
    GetUnmodifiable,
    ToArray,
    \Traversable {}
