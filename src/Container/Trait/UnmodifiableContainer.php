<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;


use Time2Split\Help\Classes\Copyable; //phpstan
use Time2Split\Help\Container\Class\Clearable; //phpstan
use Time2Split\Help\Container\Class\GetUnmodifiable; //phpstan
use Time2Split\Help\Container\Class\IsUnmodifiable;

/**
 * An implementation for an unmodifiable `ContainerBase`.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @see \Time2Split\Help\Classes\GetUnmodifiable
 * @see \Time2Split\Help\Classes\IsUnmodifiable
 * @see \Time2Split\Help\Container\ContainerBase
 * 
 * @template K
 * @template V
 * 
 * @phpstan-require-implements Clearable&Copyable&GetUnmodifiable&IsUnmodifiable
 */
trait UnmodifiableContainer
{
    use
        UnmodifiableClearable,
        UnmodifiableCopyable;

    /**
     * (`IsUnmodifiable`)
     * 
     * @return static
     * 
     * @phpstan-return $this
     */
    #[\Override]
    public function unmodifiable(): static
    {
        assert($this instanceof IsUnmodifiable);
        return $this;
    }
}
