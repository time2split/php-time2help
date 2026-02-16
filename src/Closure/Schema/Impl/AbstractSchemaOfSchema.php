<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema\Impl;

use Closure;
use Time2Split\Help\Closure\Schema\OfSchemas;
use Time2Split\Help\Closure\Schema\Schema;
use Time2Split\Help\Closure\Schema\SchemaBuilder;
use Time2Split\Help\Closure\Schema\Schemas;

abstract class AbstractSchemaOfSchema
implements
    Schema,
    OfSchemas
{
    /**
     * @var Schema[]
     */
    protected array $schemas = [];

    private ?SchemaBuilder $building;

    public function __construct(
        protected readonly null|(Schema&OfSchemas) $parent = null
    ) {}

    // ========================================================================

    private function addSchema(Schema $schema): void
    {
        $this->schemas[] = $schema;
    }

    protected final function setBuilder(?SchemaBuilder $schema): ?SchemaBuilder
    {
        return $this->building = $schema;
    }

    // ========================================================================

    public final function buildSchema(
        Schema $schema,
        ?SchemaBuilder $builder = null
    ): void {
        $this->addSchema($schema);
        $this->setBuilder($builder);
    }

    public final function buildSchemaTransformElement(
        Schema $schema,
        Closure $transform,
        ?SchemaBuilder $builder = null
    ): Schema {
        $this->buildSchema(
            Schemas::transformElement($schema, $transform),
            $builder
        );
        return $schema;
    }

    public final function buildSchemaFromClosure(
        Closure $check,
        ?SchemaBuilder $builder = null
    ): Schema {
        $this->buildSchema($schema = Schemas::fromClosure($check), $builder);
        return $schema;
    }

    // ========================================================================

    public function and(): SchemaBuilder
    {
        return $this->building;
    }

    public final function commit(): null|(Schema&OfSchemas)
    {
        return $this->parent;
    }

    // ========================================================================

    public function validate($element): bool
    {
        foreach ($this->schemas as $schema) {

            if (!$schema->validate($element))
                return false;
        }
        return true;
    }
}
