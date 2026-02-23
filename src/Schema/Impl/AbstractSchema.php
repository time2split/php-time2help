<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Impl;

use Time2Split\Help\Schema\BuildingSchema;
use Time2Split\Help\Schema\OfSchemas;

/**
 * An implementation for a schema.
 * 
 * @package time2help\schema
 */
abstract class AbstractSchema
implements
    BuildingSchema
{
    private bool $commit = false;

    /**
     * The parent schema of this schema.
     * 
     * A parent is a schema {@see OfSchemas} containing this schema as a child.
     * If no parent is set then this schema is the root parent
     * (the topmost schema).
     * 
     * The parent is only set during the building process of a schema using the fluent API.
     * When the schema is no more the subject of a fluent method then the parent is discarded.
     */
    protected null|(BuildingSchema&OfSchemas) $parent = null;

    // ========================================================================

    protected final function committed(): bool
    {
        return $this->commit;
    }

    protected function commitThis(): static
    {
        if ($this->committed())
            return $this;

        $committed = clone $this;
        $committed->commit = true;
        return $committed;
    }

    // ========================================================================

    #[\Override]
    public final function up(int $nb = 1, ?string $returnsClass = null): BuildingSchema&OfSchemas
    {
        if ($nb <= 0)
            throw new \DomainException("up($nb) value must be positive");

        $schema = $this;
        $i = 0;

        while ($i++ < $nb) {

            if ($schema->parent === null)
                throw new \OutOfRangeException("up($nb) no more parent after level $i");

            $schema = $schema->parent;
        }
        return $schema;
    }

    #[\Override]
    public final function top(?string $returnsClass = null): BuildingSchema&OfSchemas
    {
        $schema = $this;

        while ($schema->parent !== null)
            $schema = $schema->parent;

        return $schema;
    }
}
