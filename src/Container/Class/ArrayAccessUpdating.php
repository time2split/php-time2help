<?php

namespace Time2Split\Help\Container\Class;

/**
 * Utilities for an \ArrayAccess class.
 *
 * (Template`<K,V>`)
 * 
 * An implementation is provided by the trait ArrayAccessUtilities.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template V
 */
interface ArrayAccessUpdating
{
    /**
     * Updates some existant entries and add the unexistant ones.
     *
     * @param iterable<K,V> ...$listsOfEntries
     *            Lists of entries to update.
     * @return static This set.
     */
    public function updateEntries(iterable ...$listsOfEntries): static;

    /**
     * Deletes some entries (by their keys).
     *
     * @param K ...$keys
     *            The keys to drop.
     * @return static This set.
     */
    public function unsetMore(mixed ...$keys): static;

    /**
     * Deletes some entries (by their keys).
     *
     * @param iterable<K> ...$listsOfKeys
     *            Lists of keys to drop.
     * @return static This set.
     */
    public function unsetFromList(iterable ...$listsOfKeys): static;
}
