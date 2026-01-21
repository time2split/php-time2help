<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Iterables;

/**
 * Utilities for an \ArrayAccess class.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 */
trait ArrayAccessUpdating
{
    /**
     * @inheritdoc
     * @param iterable<K,V> ...$listsOfEntries
     * @return $this
     */
    #[\Override]
    public function updateEntries(iterable ...$listsOfEntries): static
    {
        $it = Iterables::append(...$listsOfEntries);

        foreach ($it as $k => $v)
            $this->offsetSet($k, $v);

        return $this;
    }

    /**
     * @inheritdoc
     * @param K ...$keys
     * @return $this
     */
    #[\Override]
    public function unsetMore(mixed ...$keys): static
    {
        foreach ($keys as $k)
            $this->offsetUnset($k);

        return $this;
    }

    /**
     * @inheritdoc
     * @param iterable<int,K> ...$listsOfKeys
     * @return $this
     */
    #[\Override]
    public function unsetFromList(iterable ...$listsOfKeys): static
    {
        $keys = Iterables::append(...$listsOfKeys);

        foreach ($keys as $k)
            $this->offsetUnset($k);

        return $this;
    }
}
