<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\Class\OfElements;
use Time2Split\Help\TriState;

/**
 * A bag data-structure to store elements with possible duplicates.
 * 
 * A bag uses the array syntax to query and modify its contents,
 * however the array syntax is only provided for facilities:
 * a bag can never be considered as an array.
 * 
 * The class {@see Bags} provides static factory methods to create instances of {@see Bag}.
 * 
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 * 
 * @template T
 * @extends ContainerAA<T,int>
 * @extends OfElements<T>
 */
interface Bag
extends
    ContainerAA,
    OfElements
{
    /**
     * Whether this set is included in another one.
     * 
     * @param Bag<T> $inside
     *     The set to check to be included in.
     * @param TriState $strictInclusion
     *  - `TriState::Yes`: The inclusion must be strict ($inside must have more elements)
     *  - `TriState::No`: The inclusion must not be strict ($inside must have the same number of elements)
     *  - `TriState::Maybe`: The inclusion may, or may not be strict
     */
    public function isIncludedIn(
        Bag $inside,
        TriState $strictInclusion = TriState::Maybe
    ): bool;

    /**
     * Whether this set contains the same elements as another one.
     * 
     * @param Bag<T> $inside
     *     The set to check to be equals to.
     */
    public function equals(
        Bag $other,
    ): bool;

    /**
     * @return IsUnmodifiable&Bag<T>
     */
    #[\Override]
    public function unmodifiable(): Bag&IsUnmodifiable;

    /**
     * Returns the number of times an item is assigned to the bag.
     * 
     * @param T $item An item.
     * @return int the number of assignations for the item.
     * @link https://www.php.net/manual/en/arrayaccess.offsetget.php ArrayAccess::offsetGet()
     */
    #[\Override]
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
    #[\Override]
    public function offsetSet($item, $nb): void;

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
