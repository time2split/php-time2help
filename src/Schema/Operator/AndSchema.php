<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Operator;

use Time2Split\Help\Schema\Impl\AbstractSchemaOfSchema;
use Time2Split\Help\Schema\Schema;

/**
 * Validates the intersection of its childs.
 * 
 * ```php
 * $child[0] && $child[1] && ... && $child[$last]
 * ```
 * 
 * @package time2help\schema
 */
class AndSchema
extends AbstractSchemaOfSchema
{
    /**
     * Add some childs in this intersection schema.
     * 
     * @param Schema $schema
     *      The first child to add.
     * @param Schema $and
     *      The second child to add.
     * @param Schema ...$andMore
     *      More childs to add.
     * @return static `$this`
     * 
     * @phpstan-return $this
     */
    #[\Override]
    public final function intersectionOf(Schema $schema, Schema $and, Schema ...$andMore): static
    {
        return $this->and($schema, ...[$and, ...$andMore]);
    }

    /**
     * Add some childs in this intersection schema.
     * 
     * @param Schema $schema
     *      The first child to add.
     * @param Schema ...$andMore
     *      More childs to add.
     * @return static `$this`
     * 
     * @phpstan-return $this
     */
    public final function and(Schema $schema, Schema ...$andMore): static
    {
        return $this->addThenCommitSchemas(...[$schema, ...$andMore]);
    }

    // ========================================================================

    #[\Override]
    public function validateElement(mixed $element): bool
    {
        foreach ($this as $schema) {

            if (!$schema->validate($element))
                return false;
        }
        return true;
    }
}
