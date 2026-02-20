<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Impl;

use Time2Split\Help\Schema\OfSchemas;

/**
 * Validates the negation of an intersection of schemas.
 * 
 * @package time2help\schema
 */
class NotSchema
extends AbstractSchemaOfSchema
{
    #[\Override]
    public function validateElement(mixed $element): bool
    {
        foreach ($this->schemas as $schema) {

            if (!$schema->validate($element))
                return true;
        }
        return false;
    }
}
