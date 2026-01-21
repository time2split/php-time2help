<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Class;

/**
 * A container of some type of elements.
 * 
 * Contrary to the array access container, here the container is centered on its elements, the keys are not considered.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template T
 * @extends ToListOfElements<T>
 * @extends ElementsUpdating<T>
 */
interface OfElements
extends
    ToListOfElements,
    ElementsUpdating {}
