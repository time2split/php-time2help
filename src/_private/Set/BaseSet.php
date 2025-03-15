<?php

declare(strict_types=1);

namespace Time2Split\Help\_private\Set;

use Time2Split\Help\Set;
use Time2Split\Help\Trait\ArrayAccessAssignItems;

/**
 * Set implementation with the utility methods.
 *
 * This implementation is common to all BaseSet instances provided by the library.
 * 
 * The class {@see Sets} provides static factory methods to create instances of {@see Set}.
 * 
 * @template T
 * @implements Set<T>
 * 
 * @internal
 * @author Olivier Rodriguez (zuri)
 */
abstract class BaseSet implements Set
{
    use ArrayAccessAssignItems;
}
