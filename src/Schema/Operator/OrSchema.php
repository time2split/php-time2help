<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Operator;

use Time2Split\Help\Schema\Impl\AbstractSchemaOfSchema;
use Time2Split\Help\Schema\Schema;

/**
 * Validates a union of schemas.
 * 
 * ```php
 * $child[0] || $child[1] || ... || $child[$last]
 * ```
 * 
 * @package time2help\schema
 */
class OrSchema
extends AbstractSchemaOfSchema
{
    /**
     * Add some childs in this union schema.
     * 
     * @param Schema $schema
     *      The first child to add.
     * @param Schema $or
     *      The second child to add.
     * @param Schema ...$orMore
     *      More childs to add.
     * @return static `$this`
     * 
     * @phpstan-return $this
     */
    #[\Override]
    public final function unionOf(Schema $schema, Schema $or, Schema ...$orMore): static
    {
        return $this->or($schema, ...[$or, ...$orMore]);
    }

    /**
     * Add some childs in this union  schema.
     * 
     * @param Schema $schema
     *      The first child to add.
     * @param Schema ...$orMore
     *      More childs to add.
     * @return static `$this`
     * 
     * @phpstan-return $this
     */
    public final function or(Schema $schema, Schema ...$orMore): static
    {
        return $this->addThenCommitSchemas(...[$schema, ...$orMore]);
    }

    // ========================================================================

    #[\Override]
    public function validateElement(mixed $element): bool
    {
        if (0 === count($this))
            return true;

        foreach ($this as $schema) {

            if ($schema->validate($element))
                return true;
        }
        return false;
    }
}
