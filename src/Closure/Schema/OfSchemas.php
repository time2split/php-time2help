<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema;

use Closure;

/**
 * phpstan-require-implements Schema
 */
interface OfSchemas
{
    function and(): SchemaBuilder;

    function commit(): null|(Schema&OfSchemas);

    function buildSchema(
        Schema $schema,
        ?SchemaBuilder $builder = null
    ): void;

    function buildSchemaTransformElement(
        Schema $schema,
        Closure $transform,
        ?SchemaBuilder $builder = null
    ): Schema;

    function buildSchemaFromClosure(
        Closure $check,
        ?SchemaBuilder $builder = null
    ): Schema;
}
