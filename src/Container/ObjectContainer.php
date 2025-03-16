<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;

/**
 * A container working like a \SplObjectStorage.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
final class ObjectContainer
extends ContainerWithStorage
implements \ArrayAccess
{
    use ArrayAccessWithStorage;

    public function __construct()
    {
        parent::__construct(new \SplObjectStorage);
    }

    private static function copySplObjectStorage(\SplObjectStorage $storage)
    {
        $ret = new \SplObjectStorage();
        $ret->addAll($storage);
        return $ret;
    }

    #[\Override]
    public function copy(): static
    {
        $copy = new self();
        $copy->storage = self::copySplObjectStorage($this->storage);
        return $copy;
    }

    #[\Override]
    public function clear(): void
    {
        $this->storage = new \SplObjectStorage;
    }
}
