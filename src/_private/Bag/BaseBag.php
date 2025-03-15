<?php

declare(strict_types=1);

namespace Time2Split\Help\_private\Bag;

use Time2Split\Help\Bag;
use Time2Split\Help\Trait\ArrayAccessAssignItems;

/**
 * @template T
 * @implements Bag<T>
 * 
 * @internal
 * @author Olivier Rodriguez (zuri)
 */
abstract class BaseBag implements Bag
{
    use ArrayAccessAssignItems;
}
