<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Class;

use Traversable;

/**
 * A container of some type of elements.
 * 
 * (Template`<T>`)
 * 
 * Contrary to the array access container the container is centered on its elements; the keys are not considered here.
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
     * @return \Traversable<int,T>
     */
    public function elements(): \Traversable;

    /**
     * Gets the elements of the container.
     * 
     * @return array<int,T>
     */
    public function toListOfElements(): array;
}
