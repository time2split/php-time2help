<?php

declare(strict_types=1);

namespace Time2Split\Help\Iterator;

use ArrayIterator;
use IteratorIterator;
use Time2Split\Help\Container\Entry;

/**
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 */
class EntryIterator extends IteratorIterator
{
    private Entry $current;

    public function __construct(Entry ...$entries)
    {
        parent::__construct(new ArrayIterator($entries));
    }

    #[\Override]
    public function valid(): bool
    {
        $valid = parent::valid();

        if (!$valid)
            return false;

        $this->current = parent::current();
        return true;
    }

    #[\Override]
    public function key(): mixed
    {
        return $this->current->key;
    }

    #[\Override]
    public function current(): mixed
    {
        return $this->current->value;
    }
}
