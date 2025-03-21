<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use ArrayAccess;

/**
 * Implementation for the interface ArrayAccessAssignItems.
 * 
 * @template K
 * @template V
 */
trait ArrayAccessAssignItems
{
    use ArrayAccessUpdating;

    /**
     * @var mixed
     */
    protected const PUT_VALUE = true;

    /**
     * @param K ...$items
     * @return $this
     */
    #[\Override]
    public  function putMore(mixed ...$items): static
    {
        foreach ($items as $item)
            $this->offsetSet($item, static::PUT_VALUE);
        return $this;
    }

    /**
     * @param iterable<K> ...$lists
     * @return $this
     */
    #[\Override]
    public  function putFromList(iterable ...$lists): static
    {
        foreach ($lists as $items) {
            foreach ($items as $item)
                $this->offsetSet($item, static::PUT_VALUE);
        }
        return $this;
    }
}
