<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation for an unmodifiable ArrayAccessContainer.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait UnmodifiableArrayAccessContainer
{
    use
        UnmodifiableContainer,
        UnmodifiableArrayAccess,
        UnmodifiableArrayAccessUpdating;

    #[\Override]
    public function copy(): static
    {
        return $this;
    }
}
