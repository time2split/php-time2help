<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

/**
 * An implementation of a \Countable using an internal storage.
 * ```
 * function count(): int
 * {
 *     return \count($this->storage);
 * }
 * ```
 * 
 * (It must have a property: `\Countable $storage`)
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @phpstan-property \Countable $storage
 *      The internal storage must be defined into the class.
 */
trait CountableWithStorage
{
    /**
     * @inheritdoc
     */
    #[\Override]
    public function count(): int
    {
        return \count($this->storage);
    }
}
