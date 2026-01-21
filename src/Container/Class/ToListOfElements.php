<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Class;

use Traversable;

/**
 * A container of some type of elements.
 * 
 * Contrary to the array access container, here the container is centered on its elements, the keys are not considered.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template T
 */
interface ToListOfElements
{
    /** 
     * Gets the elements of the container.
     * 
     * @return \Traversable<T>
     */
    public function elements(): \Traversable;

    /**
     * Gets the elements of the bag as a list.
     * 
     * @return T[]
     */
    public function toListOfElements(): array;
}
