<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema;

final class ParameterBuilder
extends StringBuilder
{

    public final function type(): TypeBuilder
    {
        return $this->doBuild->parent->type();
    }
}
