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

    /**
     * @param K $key
     * @return Entry<K,V>
     */
    public function setKey(mixed $key): Entry
    {
        return new Entry($key, $this->value);
    }

    /**
     * @param V $value
     * @return Entry<K,V>
     */
    public function setValue(mixed $value): Entry
    {
        return new Entry($this->key, $value);
    }

    /**
     * @return Entry<V,K>
     */
    public function flip(): Entry
    {
        return new self($this->value, $this->key);
    }

    /**
     * @return array<K,V>
     */
    public function toArrayEntry(): array
    {
        return [$this->key => $this->value];
    }

    /**
     * @return array<int,K|V>
     */
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

    /**
     * @return Closure(Entry,Entry):bool
     */
    public static function equalsClosure(bool $strict = false): Closure
    {
        if ($strict)
            return fn(Entry $a, Entry $b) =>
            $a === $b || ($a->key === $a->key && $a->value === $b->value);
        else
            return fn(Entry $a, Entry $b) =>
            $a === $b || $a == $b;
    }

    public static function equals(Entry $a, Entry $b, bool $strict = false): bool
    {
        if ($strict)
            return $a->key === $a->key && $a->value === $b->value;
        else
            return $a == $b;
    }

    public static function iteratorCurrent(\Iterator $it): Entry
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
