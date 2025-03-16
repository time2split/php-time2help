<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Classes\Copyable;

interface Container extends
    Clearable,
    \Countable,
    Copyable,
    ToArray,
    \Traversable {}
