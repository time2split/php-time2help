<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Closure;
use Generator;
use Iterator;
use IteratorAggregate;
use Time2Split\Help\Container\Class\FixedCount;
use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\Class\OfElements;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\ContainerWithArrayStorage;
use Time2Split\Help\Container\Trait\IteratorAggregateWithArrayStorage;
use Time2Split\Help\Container\Trait\ListOfElementsToElements;
use Time2Split\Help\Container\Trait\UnmodifiableContainerAA;
use Time2Split\Help\Container\Trait\UnmodifiableElementsUpdating;
use Time2Split\Help\Functions;

/**
 * A pair of key value representing a key => value element of an iterable. 
 * 
 * It permits to represent different kind of key than the ones allowed in a php array.
 *
 * It can be accessed like an array:
 * 
 *      [ 0 => $this->key,
 *        1 => $this->value ]
 * 
 * or
 * 
 *      [ 'key'   => $this->key,
 *        'value' => $this->value ]
 * 
 * 
 * @property-read mixed $key
 * @property-read mixed $value
 * @template K
 * @template V
 * 
 * @phpstan-immutable
 * @phpstan-property-read K $key
 * @phpstan-property-read V $value
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @implements IteratorAggregate<int,K|V>
 * @implements ContainerAA<int,K|V>
 * @implements OfElements<K|V>
 */
final class Entry
implements
    \Stringable,
    IteratorAggregate,
    ContainerAA,
    FixedCount,
    OfElements,
    IsUnmodifiable
{
    /**
     * @use ArrayAccessWithStorage<int,K|V>
     * @use ContainerWithArrayStorage<int,K|V>
     * @use IteratorAggregateWithArrayStorage<int,K|V>
     * @use ListOfElementsToElements<K|V>
     * @use UnmodifiableContainerAA<int,K|V>
     * @use UnmodifiableElementsUpdating<K|V>
     */
    use
        IteratorAggregateWithArrayStorage,
        ContainerWithArrayStorage,
        ArrayAccessWithStorage,
        ListOfElementsToElements,
        UnmodifiableContainerAA,
        UnmodifiableElementsUpdating {
        UnmodifiableContainerAA::clear insteadof ContainerWithArrayStorage;
        UnmodifiableContainerAA::offsetSet insteadof ArrayAccessWithStorage;
        UnmodifiableContainerAA::offsetUnset insteadof ArrayAccessWithStorage;
    }

    /**
     * @var array{array-key, mixed}
     */
    private array $storage;

    public function __construct(
        mixed $key,
        mixed $value,
    ) {
        $this->storage = [$key, $value];
    }

    /**
     * Counts the number of elements.
     * 
     * @return int Always returns `2`.
     * 
     * @phpstan-return 2
     */
    #[\Override]
    public function count(): int
    {
        return 2;
    }

    /**
     * @internal
     * @return K|V
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'key' => $this->storage[0],
            'value' => $this->storage[1],
            default => $this->$name,
        };
    }

    /**
     * Gets a new entry with a different key.
     * 
     * @param K $key
     * @return Entry
     * 
     * @phpstan-return static
     */
    public function setKey(mixed $key): Entry
    {
        return new Entry($key, $this->value);
    }

    /**
     * Gets a new entry with a different value.
     * 
     * @param V $value
     * @return Entry
     
     * @phpstan-return static
     */
    public function setValue(mixed $value): Entry
    {
        return new Entry($this->key, $value);
    }

    /**
     * Gets a new entry where the key and value are switched.
     * 
     * @return Entry
     
     * @phpstan-return static
     */
    public function flip(): Entry
    {
        return new self($this->value, $this->key);
    }

    /**
     * Gets an array entry.
     * 
     * @return array `[$this->key => $this->value]`
     * 
     * @phpstan-return array{K: V}
     */
    public function toArrayEntry(): array
    {
        return [$this->key => $this->value];
    }

    /**
     * @phpstan-return array{K,V}
     * 
     * @deprecated Replaced by {@see Entry::toListOfElements()}
     */
    public function toArray(): array
    {
        return $this->storage;
    }

    /**
     * @inheritdoc
     * 
     * @phpstan-return array{K,V}
     */
    #[\Override]
    public function toListOfElements(): array
    {
        return $this->storage;
    }

    #[\Override]
    public function __toString()
    {
        $k = Functions::basicToString($this->key);
        $v = Functions::basicToString($this->value);
        return "{{$k} => $v}";
    }

    // ========================================================================
    // STATIC

    /**
     * Gets a closure able to compare two entries.
     * 
     * @param bool $strict
     *  - `true`: use `===` for comparison (key and value).
     *  - `false`: use `==` for comparison (key and value).
     * @return Closure(Entry,Entry):bool
     * 
     * @phpstan-return Closure(Entry<mixed,mixed>, Entry<mixed,mixed>):bool
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

    /**
     * Whether two entries are equals.
     *  
     * @param Entry $a An entry.
     * @param Entry $b Another entry.
     * @param bool $strict
     *  - `true`: use `===` for comparison (key and value).
     *  - `false`: use `==` for comparison (key and value).
     * 
     * @phpstan-param Entry<K,V> $a
     * @phpstan-param Entry<K,V> $b
     */
    public static function equals(Entry $a, Entry $b, bool $strict = false): bool
    {
        if ($strict)
            return $a->key === $a->key && $a->value === $b->value;
        else
            return $a == $b;
    }

    /**
     * Gets the current entry of an iterator as an entry.
     * 
     * @param Iterator $it
     *      The iterator.
     * @return Entry
     *      An entry (`$it->key() => $it->current()`).
     * 
     * @phpstan-param Iterator<K,V> $it
     * @phpstan-return Entry<K,V>
     */
    public static function iteratorCurrent(Iterator $it): Entry
    {
        return new Entry($it->key(), $it->current());
    }

    /**
     * Maps the current key and value of an iterator to make an entry.
     * 
     * @param Iterator $it
     *      The iterator.
     * @param Closure(mixed $key):mixed $mapKey
     *      Maps the key.
     * @param Closure(mixed $value):mixed $mapValue
     *      Maps the value.
     * @return Entry
     *      An `Entry($mapKey($it->key()), $mapValue($it->current()))`.
     * 
     * @template MK
     * @template MV
     * 
     * @phpstan-param Iterator<K,V> $it
     * @phpstan-param Closure(K):MK $mapKey
     * @phpstan-param Closure(V):MV $mapValue
     * 
     * @phpstan-return Entry<MK,MV>
     */
    public static function iteratorCurrentClosure(
        Iterator $it,
        ?Closure $mapKey,
        ?Closure $mapValue
    ): Entry {
        return new Entry($mapKey($it->key()), $mapValue($it->current()));
    }

    // ========================================================================
    // ITERATION

    /**
     * @deprecated Replaced by {@see Entry::traverse()}
     * @phpstan-param iterable<mixed|K, V|Entry<K,V>> $listOfEntries
     * @phpstan-return Generator<K,V>
     */
    public static function traverseEntries(iterable $listOfEntries): Generator
    {
        return Entry::traverse($listOfEntries);
    }

    /**
     * Gets through a list of entries or Entry instances.
     * 
     * If the iteration detects a value of type {@see Entry} then
     * the key and value of the entry is used, elsewhere
     * the initial key and value of the iterable is used.
     * 
     * @param iterable<mixed,mixed> $entriesOrEntryInstances
     *      The list of entries of the form:
     *      - (`key => value`), or
     *      - (`#useless => Entry(key, value)`).
     * @return Generator
     *      A traversable list of (`key => value`).
     * 
     * @phpstan-param iterable<mixed|K, V|Entry<K,V>> $entriesOrEntryInstances
     * @phpstan-return Generator<K,V>
     */
    public static function traverse(iterable $entriesOrEntryInstances): Generator
    {
        foreach ($entriesOrEntryInstances as  $k => $v)
            if ($v instanceof Entry)
                yield $v->key => $v->value;
            else
                yield $k => $v;
    }

    /**
     * @deprecated Replaced by {@see Entry::toListOfEntryInstances()}
     * @phpstan-param iterable<mixed|K, V|Entry<K,V>> $listOfEntries
     * @phpstan-return Generator<int,Entry<K,V>>
     */
    public static function toTraversableEntries(iterable $listOfEntries): Generator
    {
        return Entry::toListOfEntryInstances($listOfEntries);
    }

    /**
     * Transforms into a list of Entry instances.
     * 
     * @param iterable<mixed,mixed> $entriesOrEntryInstances
     *      The entries of the form:
     *      - (`key => value`), or
     *      - (`#useless => Entry(key, value)`).
     *  
     * @return Generator
     *      A list of `Entry(key, value)` instances.
     * 
     * @phpstan-param iterable<mixed|K, V|Entry<K,V>> $entriesOrEntryInstances
     * @phpstan-return Generator<int,Entry<K,V>>
     */
    public static function toListOfEntryInstances(iterable $entriesOrEntryInstances): Generator
    {
        foreach ($entriesOrEntryInstances as  $k => $v)
            if ($v instanceof Entry)
                yield $v;
            else
                yield new Entry($k, $v);
    }

    /**
     * @deprecated Replaced by {@see Entry::traverseEntryInstances()}
     * @phpstan-param iterable<int, Entry<K,V>> $listOfEntries
     * @phpstan-return Generator<K,V>
     */
    public static function traverseListOfEntries(iterable $listOfEntries): Generator
    {
        return Entry::traverseEntryInstances($listOfEntries);
    }

    /**
     * Gets through a list of Entry instances.
     * 
     * @param iterable $entryInstances
     *      The list of {@see Entry} instances of the form:
     *      - (`#useless => Entry(key, value)`).
     * @return Generator
     *      A list of (`key => value`) entries.
     * 
     * @phpstan-param iterable<int, Entry<K,V>> $entryInstances
     * @phpstan-return Generator<K,V>
     */
    public static function traverseEntryInstances(iterable $entryInstances): Generator
    {
        foreach ($entryInstances as  $e) {
            assert($e instanceof Entry);
            yield $e->key => $e->value;
        }
    }

    /**
     * @deprecated Replaced by {@see Entry::entriesToListOfEntryInstances()}
     * 
     * @phpstan-param iterable<K,V> $listOfEntries
     * @phpstan-return Generator<int,Entry<K,V>>
     */
    public static function arrayToListOfEntries(iterable $listOfEntries): Generator
    {
        return Entry::entriesToListOfEntryInstances($listOfEntries);
    }

    /**
     * Transforms the entries into a list of Entry instances.
     * 
     * @param iterable $entries
     *      The (`key => value`) entries.
     * @return Generator
     *      A list of (`Entry(key, value)`) instances.
     * 
     * @phpstan-param iterable<K,V> $entries
     * @phpstan-return Generator<int,Entry<K,V>>
     */
    public static function entriesToListOfEntryInstances(iterable $entries): Generator
    {
        foreach ($entries as  $k => $v)
            yield new Entry($k, $v);
    }
}
