<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Exception\UnmodifiableException;

/**
 * An implementation for an unmodifiable `Clearable`.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @see \Time2Split\Help\Classes\GetUnmodifiable
 * @see \Time2Split\Help\Classes\IsUnmodifiable
 * @see \Time2Split\Help\Container\Clearable
 */
trait UnmodifiableClearable
{
    #[\Override]
    public function clear(): void
    {
        throw new UnmodifiableException;
    }
}
