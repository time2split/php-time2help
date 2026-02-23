<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema;

/**
 * Validates an element.
 * 
 * @package time2help\schema
 */
interface Schema
{
    /**
     * Validates an element.
     */
    function validate(mixed $element): bool;
}
