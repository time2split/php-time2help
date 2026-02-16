<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema;

interface Schema
{
    function validate($element): bool;
}
