<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Trait\ContainerWithArrayStorage as TraitContainerWithArrayStorage;

/**
 * A base implementation for a container with an internal array storage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
abstract class ContainerWithArrayStorage
implements
    Container,
    \IteratorAggregate
{
    use TraitContainerWithArrayStorage;

    public function __construct(
        protected array $storage
    ) {}

    #[\Override]
    public function copy(): static
    {
        return new static($this->storage);
    }
}
