<?php

namespace Time2Split\Diff;

/**
 * @author Olivier Rodriguez (zuri)
 * @package time2help\diff
 */
enum DiffInstructionType
{
    case Insert;

    case Keep;

    case Drop;
}
