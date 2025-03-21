<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Classes\UnmodifiableInstance;

/**
 * An implementation for an unmodifiable Copyable.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait UnmodifiableCopyable
{
    #[\Override]
    public function copy(): static
    {
        assert($this instanceof UnmodifiableInstance);
        return $this;
    }
}
