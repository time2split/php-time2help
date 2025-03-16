<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\_internal;

use Time2Split\Help\Container\ContainerWithContainerStorage;
use Time2Split\Help\Container\Trait\ArrayAccessAssignItems;

/**
 * @internal
 * @author Olivier Rodriguez (zuri)
 */
abstract class BagSetWithStorage
extends ContainerWithContainerStorage
{
    use ArrayAccessAssignItems;
}
