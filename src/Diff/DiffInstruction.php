<?php

declare(strict_types=1);

namespace Time2Split\Diff;

/**
 * @author Olivier Rodriguez (zuri)
 * @package time2help\diff
 */
abstract class DiffInstruction
{
    /**
     * @internal
     */
    public function __construct(
        public readonly DiffInstructionType $type,
        public readonly mixed $item
    ) {}
}
