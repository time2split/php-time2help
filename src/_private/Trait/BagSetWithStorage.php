<?php

declare(strict_types=1);

namespace Time2Split\Help\_private\Trait;

use Time2Split\Help\Container\ContainerWithContainerStorage;
use Time2Split\Help\Trait\ArrayAccessAssignItems;

/**
 * @internal
 * @author Olivier Rodriguez (zuri)
 */
abstract class BagSetWithStorage
extends ContainerWithContainerStorage
{
    use ArrayAccessAssignItems;
}
