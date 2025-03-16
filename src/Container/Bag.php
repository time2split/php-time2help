<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Container;

/**
 * A bag data-structure to store elements with possible duplicates.
 * 
 * A bag uses the array syntax to query and modify its contents,
 * however the array syntax is only provided for facilities:
 * a bag can never be considered as an array.
 * 
 * The class {@see Bags} provides static factory methods to create instances of {@see Bag}.
 * 
 * @template T
 * @extends \ArrayAccess<T,int>
 * @extends \Traversable<T>
 * 
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 */
interface Bag
extends
    Container,
    \ArrayAccess
{

    /**
     * Returns the number of times an item is assigned to the bag.
     * 
     * @param T $item An item.
     * @return int the number of assignations for the item.
     * @link https://www.php.net/manual/en/arrayaccess.offsetget.php ArrayAccess::offsetGet()
     */
    public function offsetGet($item): int;

    /**
     * Assigns or drops an item.
     * 
     * @param T $item An item.
     * @param int $nb a positive/negative number of assignations to set/drop.
     * 
     *      If the number is negative, it is used as the number of drops to do.
     *      This number of drops may be greater than the number of assignations of the items,
     *      in this case all assignations of $item will be removed.
     * @link https://www.php.net/manual/en/arrayaccess.offsetset.php ArrayAccess::offsetSet()
     */
    public function offsetSet($item, $nb): void;

    /**
     * Drops an item.
     * 
     * @param T $item An item.
     * @link https://www.php.net/manual/en/arrayaccess.offsetunset.php ArrayAccess::offsetUnset()
     */
    public function offsetUnset($item): void;

    /**
     * Whether an item is assigned to the set.
     * 
     * @param T $item An item.
     * @return bool true if the item is assigned, or false if not.
     * @link https://www.php.net/manual/en/arrayaccess.offsetexists.php ArrayAccess::offsetExists()
     */
    public function offsetExists($item): bool;

    // ========================================================================
    // Utilities
    // ========================================================================

    /**
     * Assigns multiple items.
     *
     * @param T ...$items
     *            Items to assign.
     * @return static This set.
     */
    public function setMore(...$items): static;

    /**
     * Drops multiple items.
     *
     * @param T ...$items
     *            Items to drop.
     * @return static This set.
     */
    public function unsetMore(...$items): static;

    /**
     * Assigns multiple items from multiple lists.
     *
     * @param iterable<T> ...$lists
     *            Lists of items to assign.
     * @return static This set.
     */
    public function setFromList(iterable ...$lists): static;

    /**
     * Drops multiples items from multiple lists.
     *
     * @param iterable<T> ...$lists
     *            Lists of items to drop.
     * @return static This set.
     */
    public function unsetFromList(iterable ...$lists): static;
}
