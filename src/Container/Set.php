<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\Class\OfElements;
use Time2Split\Help\TriState;

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
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\BagAndSet
 * 
 * @template T
 * @extends ContainerAA<T,bool>
 * @extends OfElements<T>
 */
interface Set
extends
    ContainerAA,
    OfElements
{

    /**
     * Whether this set is included in another one.
     * 
     * @param Set<T> $inside
     *     The set to check to be included in.
     * @param TriState $strictInclusion
     *  - `TriState::Yes`: The inclusion must be strict ($inside must have more elements)
     *  - `TriState::No`: The inclusion must not be strict ($inside must have the same number of elements)
     *  - `TriState::Maybe`: The inclusion may, or may not be strict
     */
    public function isIncludedIn(
        Set $inside,
        TriState $strictInclusion = TriState::Maybe
    ): bool;

    /**
     * Whether this set contains the same elements as another one.
     * 
     * @param Set<T> $other
     *     The set to check to be equals to.
     */
    public function equals(
        Set $other,
    ): bool;

    /**
     * @return IsUnmodifiable&Set<T>
     */
    #[\Override]
    public function unmodifiable(): Set&IsUnmodifiable;

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
