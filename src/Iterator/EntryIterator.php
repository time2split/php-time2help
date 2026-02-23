<?php

declare(strict_types=1);

namespace Time2Split\Help\Iterator;

use ArrayIterator;
use IteratorIterator;
use Time2Split\Help\Container\Entry;

/**
 * @internal
 * @package time2help\container\iterator
 * @author Olivier Rodriguez (zuri)
 * 
 * @template K
 * @template V
 * 
 * @extends IteratorIterator<K,V,ArrayIterator<Entry<K,V>>>
 */
class EntryIterator extends IteratorIterator
{
    /**
     * @var Entry<K,V>
     */
    private Entry $current;

    /**
     * @phpstan-param Entry<K,V>... $entries
     */
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

    /**
     * @phpstan-return K
     */
    #[\Override]
    public function key(): mixed
    {
        return $this->current->key;
    }

    /**
     * @phpstan-return V
     */
    #[\Override]
    public function current(): mixed
    {
        return $this->current->value;
    }
}
