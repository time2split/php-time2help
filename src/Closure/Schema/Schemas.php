<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema;

use Closure;
use Time2Split\Help\Classes\NotInstanciable;

final class Schemas
{
    use NotInstanciable;

    public static function closure(): ClosureSchema
    {
        return new ClosureSchema;
    }

    public static function parameter(): ParameterSchema
    {
        return new ParameterSchema();
    }

    // ========================================================================

    public static function fromClosure(Closure $check): Schema
    {
        return new class($check) implements Schema {

            public function __construct(
                private Closure $check,
            ) {}

            public function validate($element): bool
            {
                return ($this->check)($element);
            }
        };
    }

    public static function transformElement(Schema $schema, Closure $transform): Schema
    {
        return self::fromClosure(fn($element) => $schema->validate($transform($element)));
    }
}
