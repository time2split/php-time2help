<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

/**
 * A container accessible like an array.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\interface
 * 
 * @template K
 * @template V
 * @template I
 * @template AAK
 * @template AAV
 * 
 * @extends AAccess<AAK,AAV>
 * @extends ContainerBase<V,I>
 */
interface ContainerAA
extends
    AAccess,
    ContainerBase {}
