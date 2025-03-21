<?php

namespace Time2Split\Diff;

enum DiffInstructionType
{
    case Insert;

    case Keep;

    case Drop;
}
