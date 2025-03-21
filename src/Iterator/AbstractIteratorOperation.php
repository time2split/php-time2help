<?php

declare(strict_types=1);

namespace Time2Split\Help\Iterator;

use Iterator;
use OuterIterator;
use Time2Split\Help\Container\Entry;

/**
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractIteratorOperation implements Iterator
{
    abstract protected function op(mixed $key, mixed $value): Entry|Iterator;

    protected int $i;

    private ?Entry $current;

    private Iterator $first;

    private Iterator $it;

    public function __construct(Iterator $iterator)
    {
        $this->first = $iterator;
        $this->it = $iterator;
    }

    #[\Override]
    public function rewind(): void
    {
        $this->current = null;
        $this->i = 0;
        $this->it = $this->first;
        $this->it->rewind();
    }

    #[\Override]
    public function valid(): bool
    {
        if (null !== $this->current)
            return true;

        if ($this->it !== $this->first) {

            if (!$this->it->valid()) {
                $this->it = $this->first;
                $this->it->next();
                return $this->valid();
            } else {
                $this->current = new Entry(
                    $this->it->key(),
                    $this->it->current()
                );
            }
        } else {

            if (!$this->it->valid()) {
                return false;
            }
            $res = $this->op(
                $this->it->key(),
                $this->it->current()
            );

            if ($res instanceof Entry) {
                $this->current = $res;
            } else {
                $this->it = $res;
                $this->it->rewind();
                return $this->valid();
            }
        }
        $this->i++;
        return true;
    }

    #[\Override]
    public function next(): void
    {
        $this->current = null;
        $this->it->next();
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
