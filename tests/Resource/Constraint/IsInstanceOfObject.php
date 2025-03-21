<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Resource\Constraint;

use function sprintf;

use PHPUnit\Framework\Constraint\Constraint;

final class IsInstanceOfObject extends Constraint
{
    public function __construct(private readonly object $instance) {}

    public function toString(): string
    {
        return sprintf(
            'is an instance of %s',
            \get_class($this->instance),
        );
    }

    protected function matches(mixed $other): bool
    {
        if ($other instanceof $this->instance)
            return true;

        return
            get_parent_class($this->instance)
            ===
            get_parent_class($other);
    }

    protected function failureDescription(mixed $other): string
    {
        return $this->valueToTypeStringFragment($other) . $this->toString();
    }
}
