<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation for an unmodifiable container.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait UnmodifiableContainer
{
    use
        UnmodifiableClearable,
        UnmodifiableCopyable;
}
