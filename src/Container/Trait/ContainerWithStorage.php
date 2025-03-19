<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation of a Container using an internal storage.
 * 
 * @var array|ArrayAccess $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait ContainerWithStorage
{
    use
        ClearableWithStorage,
        CountableWithStorage,
        IteratorAggregateWithStorage,
        ToArrayToArrayContainer,
        ToArrayWithStorage;
}
