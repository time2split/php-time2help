<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\_internal;

use Time2Split\Help\Container\Bag;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;

/**
 * @internal
 * @author Olivier Rodriguez (zuri)
 */
abstract class BagWithStorage
extends BagSetWithStorage
implements Bag
{
    use ArrayAccessWithStorage;

    private $count = 0;

    #[\Override]
    public function offsetGet(mixed $item): int
    {
        return $this->storage[$item] ?? 0;
    }

    #[\Override]
    public function offsetSet(mixed $item, mixed $value): void
    {
        if (\is_bool($value))
            $nb = $value ? 1 : -1;
        elseif (!\is_integer($value))
            $nb = $value;
        else
            throw new \InvalidArgumentException('Must be a boolean');

        $nb > 0 ?
            $this->put($item, $nb) :
            $this->drop($item, -$nb);
    }

    private function put(mixed $item, int $nb): void
    {
        $this->count += $nb;

        if (isset($this->storage[$item]))
            $this->storage[$item] += $nb;
        else
            $this->storage[$item] = $nb;
    }

    private function drop(mixed $item, int $nb): void
    {
        $nbset = $this[$item];

        if ($nbset === 0)
            return;

        if ($nb >= $nbset) {
            unset($this->storage[$item]);
            $this->count -= $nbset;
        } else {
            $this->storage[$item] -= $nb;
            $this->count -= $nb;
        }
    }
}
