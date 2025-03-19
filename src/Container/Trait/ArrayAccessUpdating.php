<?php

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Iterables;

/**
 * Utilities for an \ArrayAccess class.
 *
 * An implementation is provided by the trait ArrayAccessUtilities.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait ArrayAccessUpdating
{
    /**
     * @inheritdoc
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
