<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use ArrayAccess;
use Time2Split\Help\Container\Trait\ContainerWithStorage as TraitContainerWithStorage;

/**
 * A base implementation for a container with an internal storage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
abstract class ContainerWithStorage
implements
    Container,
    \IteratorAggregate
{
    use TraitContainerWithStorage;

    public function __construct(
        protected mixed $storage
    ) {}
}
