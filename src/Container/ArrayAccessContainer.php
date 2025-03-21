<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use ArrayAccess;

/**
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 * 
 * @template K
 * @template V
 * 
 * @extends ArrayAccess<K,V>
 * @extends ArrayAccessUpdating<K,V>
 * @extends Container<K,V>
 */
interface ArrayAccessContainer extends
    ArrayAccess,
    ArrayAccessUpdating,
    Container {}
