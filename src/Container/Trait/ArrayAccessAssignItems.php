<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * Implementation for the interface ArrayAccessAssignItems.
 */
trait ArrayAccessAssignItems
{
    use ArrayAccessUpdateMethods;

    protected const PUT_VALUE = true;

    #[\Override]
    public  function putMore(mixed ...$items): static
    {
        foreach ($items as $item)
            $this->offsetSet($item, static::PUT_VALUE);
        return $this;
    }

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
