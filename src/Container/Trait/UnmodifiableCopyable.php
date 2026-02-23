<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\Class\Copyable; //phpstan
use Time2Split\Help\Container\Class\IsUnmodifiable;

/**
 * An implementation for an unmodifiable Copyable.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @phpstan-require-implements Copyable<K,V>
 */
trait UnmodifiableCopyable
{
    /**
     * Gets this unmodifiable instance.
     * 
     * @phpstan-return $this
     * 
     * @return static&IsUnmodifiable
     */
    #[\Override]
    public function copy(): static
    {
        assert($this instanceof IsUnmodifiable);
        return $this;
    }
}
