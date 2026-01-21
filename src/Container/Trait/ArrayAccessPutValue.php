<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * Implementation for the interface ArrayAccessPutKey.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template V
 */
trait ArrayAccessPutValue
{
    /**
     * @param V ...$items
     * @return $this
     */
    #[\Override]
    public  function putMore(mixed ...$items): static
    {
        foreach ($items as $item)
            $this->offsetSet(null, $item);
        return $this;
    }

    /**
     * @param iterable<V> ...$lists
     * @return $this
     */
    #[\Override]
    public  function putFromList(iterable ...$lists): static
    {
        foreach ($lists as $items) {
            foreach ($items as $item)
                $this->offsetSet(null, $item);
        }
        return $this;
    }
}
