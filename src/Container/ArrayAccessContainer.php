<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

interface ArrayAccessContainer extends
    \ArrayAccess,
    ArrayAccessUpdateMethods,
    Container {}
