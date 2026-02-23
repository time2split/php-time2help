<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\Class\Clearable; //phpstan

/**
 * An implementation of Clearable that call the storage clear method.
 * 
 * (It must have a property: `Clearable $storage`)
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @phpstan-require-implements Clearable
 */
trait ClearableWithStorage
{
    #[\Override]
    public function clear(): void
    {
        $this->storage->clear();
    }
}
