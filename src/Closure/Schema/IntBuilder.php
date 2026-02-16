<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema;

class IntBuilder
extends SchemaBuilder
{
    public function beetween(int $min, int $max, bool $inclusive = true): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            $inclusive
                ? fn(int $i) => $min <= $i && $i <= $max
                : fn(int $i) => $min < $i && $i < $max
        );
    }
}
