<?php

namespace Time2Split\Help\Container\Class;

/**
 * Methods to put some items in a container.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template T
 */
interface ElementsUpdating
{
    /**
     * Assigns multiple items.
     *
     * @param T ...$items
     *            Items to assign.
     * @return static This container.
     */
    public function putMore(...$elements): static;

    /**
     * Assigns multiple items from multiple lists.
     *
     * @param iterable<T> ...$lists
     *            Lists of items to assign.
     * @return static This container.
     */
    public function putFromList(iterable ...$listsOfElements): static;
}
