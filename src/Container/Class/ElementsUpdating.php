<?php

namespace Time2Split\Help\Container\Class;

/**
 * Methods to put some elements in a container.
 * 
 * (of `T`)
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template T
 */
interface ElementsUpdating
{
    /**
     * Assigns multiple elements.
     *
     * @param T ...$elements
     *            Elements to assign.
     * @return static This container.
     */
    public function putMore(...$elements): static;

    /**
     * Assigns multiple elements from multiple lists.
     *
     * @param iterable<T> ...$listsOfElements
     *            Lists of elements to assign.
     * @return static This container.
     */
    public function putFromList(iterable ...$listsOfElements): static;
}
