<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Closure;
use Time2Split\Help\Container\Trait\ToArrayToArrayContainer;
use Time2Split\Help\Functions;

/**
 * A pair of key value representing a key => value element of a container. 
 * 
 * It permits to represent different kind of key than the ones allowed in a php array.
 *
 * @template K
 * @template V
 * @implements ToArray<K,V>
 * 
 * @immutable
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
final class Entry
implements
    \Stringable,
    ToArray
{
    use ToArrayToArrayContainer;

    public function __construct(
        public readonly mixed $key,
        public readonly mixed $value,
    ) {}

    public function flip(): Entry
    {
        return new self($this->value, $this->key);
    }

    public function toArrayEntry(): array
    {
        return [$this->key => $this->value];
    }

    #[\Override]
    public function toArray(): array
    {
        return [$this->key, $this->value];
    }

    #[\Override]
    public function __toString()
    {
        $k = Functions::basicToString($this->key);
        $v = Functions::basicToString($this->value);
        return "{{$k} => $v}";
    }

    public static function iteratorCurrent(\Iterator $it,): Entry
    {
        return new Entry($it->key(), $it->current());
    }

    public static function iteratorCurrentClosure(
        \Iterator $it,
        ?Closure $makeKey,
        ?Closure $makeValue
    ): Entry {
        return new Entry($makeKey($it->key()), $makeValue($it->current()));
    }

    public static function traverseEntries(iterable $listOfEntries): \Generator
    {
        foreach ($listOfEntries as  $k => $v)
            if ($v instanceof Entry)
                yield $v->key => $v->value;
            else
                yield $k => $v;
    }

    public static function toTraversableEntries(iterable $listOfEntries): \Generator
    {
        foreach ($listOfEntries as  $k => $v)
            if ($v instanceof Entry)
                yield $v;
            else
                yield new Entry($k, $v);
    }

    public static function traverseListOfEntries(iterable $listOfEntries): \Generator
    {
        foreach ($listOfEntries as  $e) {
            assert($e instanceof Entry);
            yield $e->key => $e->value;
        }
    }

    public static function arrayToListOfEntries(iterable $listOfEntries): \Generator
    {
        foreach ($listOfEntries as  $k => $v)
            yield new Entry($k, $v);
    }
}
