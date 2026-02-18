<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Impl;

use Time2Split\Help\Schema\OfSchemas;
use Time2Split\Help\Schema\Schema;

/**
 * An implementation for a schema.
 * 
 * @package time2help\schema
 */
abstract class AbstractSchema
implements
    Schema
{
    public function __construct(
        /**
         * The parent schema of this schema.
         * 
         * A parent is a schema {@see OfSchemas}  containing this schema as a child.
         * If no parent is set then this schema is the root parent
         * (the topmost schema).
         */
        protected readonly null|(Schema&OfSchemas) $parent = null
    ) {}

    /**
     * Set the parent to null.
     * 
     * @internal
     */
    protected final function forgetParent(): void
    {
        /** @phpstan-ignore property.readOnlyAssignNotInConstructor */
        $this->parent = null;
    }
}
