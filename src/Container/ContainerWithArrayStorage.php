<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use IteratorAggregate;
use Time2Split\Help\Container\Trait\ContainerWithArrayStorage as TraitContainerWithArrayStorage;

/**
 * A base implementation for a container with an internal array storage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 * @implements Container<K,V>
 * @implements IteratorAggregate<K,V>
 */
abstract class ContainerWithArrayStorage
implements
    Container,
    IteratorAggregate
{
    /**
     * @use TraitContainerWithArrayStorage<K,V>
     */
    use TraitContainerWithArrayStorage;

    /**
     * @param array<K,V> $storage
     */
    public function __construct(
        protected array $storage
    ) {}

    #[\Override]
    public function copy(): static
    {
        /* @phpstan-ignore return.type*/
        return new static($this->storage);
    }
}
