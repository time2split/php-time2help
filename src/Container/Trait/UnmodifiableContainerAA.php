<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation for an unmodifiable `ContainerAA`.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @see \Time2Split\Help\Classes\GetUnmodifiable
 * @see \Time2Split\Help\Classes\IsUnmodifiable
 * @see \Time2Split\Help\Container\ContainerAA
 */
trait UnmodifiableContainerAA
{
    use
        UnmodifiableContainer,
        UnmodifiableArrayAccess,
        UnmodifiableArrayAccessUpdating;
}
