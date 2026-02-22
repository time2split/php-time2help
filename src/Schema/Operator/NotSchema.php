<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Operator;

/**
 * Validates the negation of an intersection of schemas.
 * 
 * ```php
 * !($child[0] && $child[1] && ...  && $child[$last])
 * ```
 * 
 * @package time2help\schema
 */
class NotSchema
extends AndSchema
{
    #[\Override]
    public function validateElement(mixed $element): bool
    {
        foreach ($this as $schema) {

            if (!$schema->validate($element))
                return true;
        }
        return false;
    }
}
