<?php

declare(strict_types=1);

namespace Time2Split\Diff;

abstract class DiffInstruction
{
    public function __construct(
        public readonly DiffInstructionType $type,
        public readonly mixed $item
    ) {}
}
