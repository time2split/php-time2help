<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation for an unmodifiable `ContainerBase`.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @see \Time2Split\Help\Classes\GetUnmodifiable
 * @see \Time2Split\Help\Classes\IsUnmodifiable
 * @see \Time2Split\Help\Container\ContainerBase
 */
trait UnmodifiableContainer
{
    use
        UnmodifiableClearable,
        UnmodifiableCopyable;

    #[\Override]
    public function unmodifiable(): static
    {
        return $this;
    }
}
