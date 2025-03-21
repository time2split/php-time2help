<?php

namespace Time2Split\Help\Container;

/**
 * Utilities for an \ArrayAccess class.
 *
 * An implementation is provided by the trait ArrayAccessUtilities.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
interface ArrayAccessUpdating
{
    /**
     * Updates some existant entries and add the unexistant ones.
     *
     * @param iterable<T> ...$listsOfEntries
     *            Lists of entries to update.
     * @return static This set.
     */
    public function updateEntries(iterable ...$listsOfEntries): static;

    /**
     * Deletes some entries (by their keys).
     *
     * @param iterable<T> ...$keys
     *            The keys to drop.
     * @return static This set.
     */
    public function unsetMore(mixed ...$keys): static;

    /**
     * Deletes some entries (by their keys).
     *
     * @param iterable<T> ...$lists
     *            Lists of keys to drop.
     * @return static This set.
     */
    public function unsetFromList(iterable ...$listsOfKeys): static;
}
