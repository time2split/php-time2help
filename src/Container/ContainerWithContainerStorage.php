<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use IteratorAggregate;
use Time2Split\Help\Container\Trait\ContainerWithContainerStorage as TraitContainerWithContainerStorage;

/**
 * A base implementation for a container with an internal Container storage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 * @implements Container<K,V>
 * @implements IteratorAggregate<K,V>
 */
abstract class ContainerWithContainerStorage
implements
    Container,
    IteratorAggregate
{
    /**
     * @use TraitContainerWithContainerStorage<K,V>
     */
    use TraitContainerWithContainerStorage;

    /**
     * @param Container<K,V> $storage
     */
    public function __construct(
        protected Container $storage
    ) {}

    /**
     * @return static<K,V>
     */
    #[\Override]
    public function copy(): static
    {
        return new static($this->storage->copy());
    }
}
