<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

trait ArrayAccessAssignItems
{
    public final function setMore(...$items): static
    {
        foreach ($items as $item)
            $this->offsetSet($item, true);
        return $this;
    }

    public final function unsetMore(...$items): static
    {
        foreach ($items as $item)
            $this->offsetUnset($item);
        return $this;
    }

    public final function setFromList(iterable ...$lists): static
    {
        foreach ($lists as $items) {
            foreach ($items as $item)
                $this->offsetSet($item, true);
        }
        return $this;
    }

    public final function unsetFromList(iterable ...$lists): static
    {
        foreach ($lists as $items) {
            foreach ($items as $item)
                $this->offsetUnset($item);
        }
        return $this;
    }
}
