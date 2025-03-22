<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Countable;

/**
 * An implementation of a \Countable using an internal storage.
 * ```
 * function count(): int
 * {
 *     return \count($this->storage);
 * }
 * ```
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @param Countable $storage The internal storage must be defined into the class.
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
