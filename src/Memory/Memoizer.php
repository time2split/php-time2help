<?php

declare(strict_types=1);

namespace Time2Split\Help\Memory;

use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\ContainerBase;

/**
 * @author Olivier Rodriguez (zuri)
 * @package time2help\memory
 * 
 * @template ID
 * @template M
 * 
 * @extends ContainerBase<ID,M>
 */
interface Memoizer extends ContainerBase
{
    /**
     * @phpstan-return Memoizer<ID,M>&IsUnmodifiable
     */
    #[\Override]
    public function unmodifiable(): Memoizer&IsUnmodifiable;
}
