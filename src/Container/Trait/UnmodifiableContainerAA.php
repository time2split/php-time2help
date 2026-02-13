<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\ContainerAA; //phpstan

/**
 * An implementation for an unmodifiable `ContainerAA`.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @see \Time2Split\Help\Classes\GetUnmodifiable
 * @see \Time2Split\Help\Classes\IsUnmodifiable
 * @see \Time2Split\Help\Container\ContainerAA
 * 
 * @template K
 * @template V
 * 
 * @phpstan-require-implements ContainerAA<K,V>
 */
trait UnmodifiableContainerAA
{
    /**
     * @use UnmodifiableContainer<K,V>
     * @use UnmodifiableArrayAccess<K,V>
     * @use UnmodifiableArrayAccessUpdating<K,V>
     */
    use
        UnmodifiableContainer,
        UnmodifiableArrayAccess,
        UnmodifiableArrayAccessUpdating;
}
