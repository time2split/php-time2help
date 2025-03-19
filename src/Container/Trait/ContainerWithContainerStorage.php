<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\Container;

/**
 * An implementation of a Container using an internal Container storage.
 * 
 * @var Container $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait ContainerWithContainerStorage
{
    use
        ClearableWithStorage,
        CountableWithStorage,
        IteratorAggregateWithStorage,
        IteratorToArray,
        IteratorToArrayContainer;
}
