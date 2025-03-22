<?php

namespace Time2Split\Help\Container;

/**
 * Methods to put some items in a container.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\interface
 * 
 * @template T
 */
interface ContainerPutMethods
{
    /**
     * Assigns multiple items.
     *
     * @param T ...$items
     *            Items to assign.
     * @return static This set.
     */
    public function putMore(...$items): static;

    /**
     * Assigns multiple items from multiple lists.
     *
     * @param iterable<T> ...$lists
     *            Lists of items to assign.
     * @return static This set.
     */
    public function putFromList(iterable ...$lists): static;
}
