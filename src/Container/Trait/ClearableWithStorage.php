<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\Clearable;

/**
 * An implementation of Clearable that call the storage clear method.
 * 
 * The class must be Clearable.
 * 
 * ```
 * public function clear(): void
 * {
 *     $this->storage->clear();
 * }
 * ```
 * 
 * @var Clearable $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait ClearableWithStorage
{
    #[\Override]
    public function clear(): void
    {
        $this->storage->clear();
    }
}
