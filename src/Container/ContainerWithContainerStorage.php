<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Trait\ContainerWithContainerStorage as TraitContainerWithContainerStorage;
use Time2Split\Help\Container\Trait\IteratorToArray;
use Time2Split\Help\Container\Trait\IteratorToArrayContainer;

/**
 * A base implementation for a container with an internal Container storage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
abstract class ContainerWithContainerStorage
implements
    Container,
    \IteratorAggregate
{
    use TraitContainerWithContainerStorage;

    public function __construct(
        protected Container $storage
    ) {}

    #[\Override]
    public function copy(): static
    {
        return new static($this->storage->copy());
    }
}
