<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

/**
 * A set data-structure to store elements without duplicates.
 * 
 * Each element in a set is unique according to a comparison operation.
 * The comparison operation depends on the implementation of the set.
 * 
 * A set uses the array syntax to query and modify its contents,
 * however the array syntax is only provided for facilities:
 * a set can never be considered as an array.
 *
 * The class {@see Sets} provides static factory methods to create instances of {@see Set}.
 * 
 * @package time2help\container\interface
 * @author Olivier Rodriguez (zuri)
 * 
 * @template T
 * @extends ArrayAccessUpdating<T,bool>
 * @extends ContainerAA<bool,T, Set<T>, int, T>
 * @extends ContainerPutMethods<T>
 */
interface Set
extends
    ArrayAccessUpdating,
    ContainerAA,
    ContainerPutMethods,
    FetchingClosed
{
    /**
     * Whether an item is assigned to the set.
     * 
     * @param T $item An item.
     * @return bool true if the item is assigned, or false if not.
     * @link https://www.php.net/manual/en/arrayaccess.offsetget.php ArrayAccess::offsetGet()
     */

    #[\Override]
    public function offsetGet($item): bool;

    /**
     * Assigns or drops an item.
     * 
     * @param T $item An item.
     * @param bool $value true to add the item, or false to drop it.
     * @link https://www.php.net/manual/en/arrayaccess.offsetset.php ArrayAccess::offsetSet()
     */
    #[\Override]
    public function offsetSet($item, $value): void;

    /**
     * Drops an item.
     * 
     * @param T $item An item.
     * @link https://www.php.net/manual/en/arrayaccess.offsetunset.php ArrayAccess::offsetUnset()
     */
    #[\Override]
    public function offsetUnset($item): void;

    /**
     * Whether an item is assigned to the set.
     * 
     * @param T $item An item.
     * @return bool true if the item is assigned, or false if not.
     * @link https://www.php.net/manual/en/arrayaccess.offsetexists.php ArrayAccess::offsetExists()
     */
    #[\Override]
    public function offsetExists($item): bool;
}
