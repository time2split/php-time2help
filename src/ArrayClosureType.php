<?php

declare(strict_types=1);

namespace Time2Split\Help;

/**
 * Type of closure for array methods.
 * 
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 */
enum ArrayClosureType
{
    case Key;

    case Value;

    case Entry;
}
