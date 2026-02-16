<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema\Impl;

use Time2Split\Help\Closure\Schema\OfSchemas;
use Time2Split\Help\Closure\Schema\Schema;

abstract class AbstractSchema
implements
    Schema
{
    public function __construct(
        protected readonly null|(Schema&OfSchemas) $parent
    ) {}
}
