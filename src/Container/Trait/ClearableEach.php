<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation of Clearable that drops every element one by one.
 * 
 * The class must be \Traversable.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait ClearableEach
{
    #[\Override]
    public function clear(): void
    {
        foreach ($this as $k => $v)
            unset($this[$k]);
    }
}
