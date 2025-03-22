<?php

declare(strict_types=1);

namespace Time2Split\Help;

use AppendIterator;
use ArrayIterator;
use Closure;
use EmptyIterator;
use Generator;
use Iterator;
use Time2Split\Help\Cast\Cast;
use Time2Split\Help\Cast\Ensure;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Iterable\ParallelFlag;
use Time2Split\Help\Iterator\AbstractIteratorOperation;
use Time2Split\Help\Iterator\AbstractParallelIteratorOperation;
use Time2Split\Help\Iterator\EntryIterator;

/**
 * Functions on iterables.
 *
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 */
final class Iterables
{
    use NotInstanciable;

    private static function getCallabackStrategy(Closure $closure, bool $hasSubject): Closure
    {
        // Todo: more specific call when returning void
        $reflect = new \ReflectionFunction($closure);
        $params = $reflect->getParameters();

        if (empty($params))
            return $closure;

        $firstType = $params[0]->getType();

        if ((string)$firstType === Entry::class)

            if ($hasSubject)
                return fn(mixed $v, mixed $k, iterable &$subject) => $closure(new Entry($k, $v), $subject);
            else
                return fn(mixed $v, mixed $k) => $closure(new Entry($k, $v));

        return $closure;
    }

    // ========================================================================
    // OPERATIONS
    // ========================================================================

    /**
     * Iterates until a predicate is verified.
     * 
     * @template K
     * @template V
     * 
     * @param iterable<K,V> $iterable The iterable to walk through.
     * @param Closure $predicate
     *  Stops the iteration when true is returned.
     *  - predicate(K $akey, V $aval, iterable &$iterable):bool, or
     *  - predicate(Entry $e, iterable &$iterable):bool
     * @return bool true if the end of the iterable is reached, false otherwise.
     */
    public static function walksUntil(iterable $iterable, Closure $predicate): bool
    {
        $predicate = self::getCallabackStrategy($predicate, true);

        foreach ($iterable as $k => $v) {
            if ($predicate($v, $k, $iterable))
                return false;
        }
        return true;
    }

    /**
     * Checks if if at least one entry satisfy a predicate function.
     * 
     * @template K
     * @template V
     * 
     * @param iterable<K,V> $iterable The iterable to walk through.
     * @param Closure $predicate
     *  Stops the iteration when true is returned.
     *  - predicate(K $akey, V $aval):bool, or
     *  - predicate(Entry $e):bool
     * @return bool true if the predicate is true for one entry.
     * Otherwise returns false.
     */
    public static function any(iterable $iterable, Closure $predicate): bool
    {
        $predicate = self::getCallabackStrategy($predicate, false);

        if (\is_array($iterable))
            return \array_any($iterable, $predicate);

        foreach ($iterable as $k => $v) {
            if ($predicate($v, $k))
                return true;
        }
        return false;
    }

    /**
     * Checks if all entries satisfy a predicate function.
     * 
     * @template K
     * @template V
     * 
     * @param iterable<K,V> $iterable The iterable to walk through.
     * @param Closure $predicate
     *  Stops the iteration when false is returned.
     *  - predicate(K $akey, V $aval):bool, or
     *  - predicate(Entry $e):bool
     * @return bool 
     * Returns true if the predicate is true for all entries
     *  or if the iterable is empty.
     * Otherwise returns false.
     */
    public static function all(iterable $iterable, Closure $predicate): bool
    {
        $predicate = self::getCallabackStrategy($predicate, false);

        if (\is_array($iterable))
            return \array_all($iterable, $predicate);

        foreach ($iterable as $k => $v) {
            if (!$predicate($v, $k))
                return false;
        }
        return true;
    }

    /**
     * Counts the number of entries of an iterable.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable The iterable to walk through.
     * @param bool $allowCountable Allow to use \count($iterable) if the sequence is \Countable.
     * @return int The number of entries.
     */
    public static function count(iterable $iterable, bool $allowCountable = false): int
    {
        if (\is_array($iterable) || ($allowCountable && $iterable instanceof \Countable))
            return \count($iterable);

        $i = 0;
        foreach ($iterable as $NotUsed)
            $i++;
        return $i;
    }

    // ========================================================================
    // UNARY
    // ========================================================================

    /**
     * Iterable on keys.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable The iterable to walk through.
     * @return iterable<int,K> A list of keys.
     *  If $iterable is an array then returns an array, otherwise returns an Iterator.
     */
    public static function keys(iterable $iterable): iterable
    {
        if (\is_array($iterable))
            return \array_keys($iterable);

        return new class(Cast::iterableToIterator($iterable)) extends AbstractIteratorOperation {
            protected function op(mixed $key, mixed $value): Entry
            {
                return new Entry($this->i, $key);
            }
        };
    }

    /**
     * Iterable on values.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable The iterable to walk through.
     * @return iterable<int,V> A list of values.
     *  If $iterable is an array then returns an array, otherwise returns an Iterator.
     */
    public static function values(iterable $iterable): iterable
    {
        if (\is_array($iterable))
            return \array_values($iterable);

        return new class(Cast::iterableToIterator($iterable)) extends AbstractIteratorOperation {
            protected function op(mixed $key, mixed $value): Entry
            {
                return new Entry($this->i, $value);
            }
        };
    }

    // ========================================================================

    /**
     * An iterable on entries in reverse order.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable The iterable to walk through.
     * @return iterable<K,V> The reversed entries.
     *  If $iterable is an array or is empty, then it returns an array,
     *  otherwise returns an Iterator.
     */
    public static function reverse(iterable $iterable): iterable
    {
        if (\is_array($iterable))
            return \array_reverse($iterable, true);

        foreach ($iterable as $k => $v) {
            $keys[] = $k;
            $values[] = $v;
        }
        if (!isset($keys))
            return [];

        /**
         * @phpstan-ignore variable.undefined
         */
        return self::combine(\array_reverse($keys), \array_reverse($values));
    }

    /**
     * An iterable on keys in reverse order.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable The iterable to walk through.
     * @return iterable<int,K> A list of reversed keys.
     *  If $iterable is an array or is empty, then it returns an array,
     *  otherwise returns an Iterator.
     */
    public static function reverseKeys(iterable $iterable): iterable
    {
        if (\is_array($iterable))
            return \array_reverse(\array_keys($iterable));

        foreach ($iterable as $k => $v) {
            $keys[] = $k;
        }
        if (!isset($keys))
            return [];

        return new ArrayIterator(
            \array_values(\array_reverse($keys))
        );
    }

    /**
     * An iterable on values in reverse order.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable The iterable to walk through.
     * @return iterable<int,V> A list of reversed values.
     *  If $iterable is an array or is empty, 
     *  then it returns an array, otherwise returns an Iterator.
     */
    public static function reverseValues(iterable $iterable): iterable
    {
        if (\is_array($iterable))
            return \array_values(\array_reverse($iterable));

        foreach ($iterable as $v) {
            $values[] = $v;
        }
        if (!isset($values))
            return [];

        return new ArrayIterator(
            \array_values(\array_reverse($values))
        );
    }

    /**
     * An iterable on entries reversing their key with their value (ie: $val => $key).
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable The iterable to walk through.
     * @return iterable<V,K> The flipped entries.
     *  If $iterable is an array then returns an array, otherwise returns an Iterator.
     */
    public static function flip(iterable $iterable): iterable
    {
        if (\is_array($iterable))
            return \array_flip($iterable);

        return new class(Cast::iterableToIterator($iterable)) extends AbstractIteratorOperation {
            protected function op(mixed $key, mixed $value): Entry
            {
                return new Entry($value, $key);
            }
        };
    }

    /**
     * An iterator over the flipped entries of an iterable in reverse order.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable The iterable to walk through.
     * @return iterable<V,K> The flipped entries.
     *  If $iterable is an array then returns an array, otherwise returns an Iterator.
     * @see Traversables::flip()
     */
    public static function reverseFlip(iterable $iterable): iterable
    {
        return self::flip(self::reverse($iterable));
    }

    // ========================================================================
    // COMBINE MULTIPLE
    // ========================================================================

    /**
     * Iterates over several iterables one after the other.
     * 
     * @template K
     * @template V
     * @param iterable<K,V> ...$iterables The iterables to iterate through.
     * @return iterable<K,V> The iterable of iterators.
     */
    public static function append(iterable ...$iterables): iterable
    {
        if (\count($iterables) === 1)
            return $iterables[0];

        /**
         * @var AppendIterator<K,V,Iterator<K,V>>
         */
        $it = new AppendIterator();

        foreach ($iterables as $i)
            $it->append(Cast::iterableToIterator($i));

        return $it;
    }

    /**
     * Combine two iterators, one representing the keys and the other the values,
     * to iterate through the obtained entries.
     *
     * @template K
     * @template V
     * @param iterable<int,K> $keys The keys.
     * @param iterable<int,V> $values The values.
     * @param ParallelFlag $flag The flag to set.
     * @return Iterator<K,V> The combined entries.
     */
    public static function combine(
        iterable $keys,
        iterable $values,
        ParallelFlag $flag = ParallelFlag::NEED_ANY
    ): Iterator {
        if (\is_array($keys) && \is_array($values) && is_list_of_array_keys($keys))
            return new ArrayIterator(\array_combine($keys, $values));

        return new class($flag, [$keys, $values]) extends AbstractParallelIteratorOperation {
            protected function op(mixed $key, mixed $value): Entry
            {
                return new Entry($value[0], $value[1]);
            }
        };
    }

    /**
     * An iterator over the entries obtained from a MultipleIterator.
     *
     * Example
     * ```php
     * $a = [10,20,30];
     * $b = ['a' => 'A', 'b' => 'B', 'c' => 'C'];
     * $it = Iterables::parallel($a,$b);
     * error_dump_basic($it);
     * ```
     * Displays
     * ```txt
     * // [ keys ] => [ values ]
     * <[
     * [ 0, a ] => [ 10, A ],
     * [ 1, b ] => [ 20, B ],
     * [ 2, c ] => [ 30, C ]
     * ]>
     * ```
     * 
     * @template K
     * @template V
     * @param iterable<int,iterable<K,V>> $iterables The iterables to iterate through.
     * @param ParallelFlag $flag The flag to set.
     * @return Iterator<K,V> The multiple ([ keys ] => [ values ]) entries.
     * 
     * @link https://www.php.net/manual/en/class.multipleiterator.php MultipleIterator
     */
    public static function multiple(
        iterable $iterables,
        ParallelFlag $flag = ParallelFlag::NEED_ANY,
    ): Iterator {
        return new class($flag, $iterables) extends AbstractParallelIteratorOperation {
            protected function op(mixed $key, mixed $value): Entry
            {
                return new Entry($key, $value);
            }
        };
    }

    /**
     * Iterate in parallel through multiple iterables.
     * 
     * Example
     * ```php
     * $a = [10,20,30];
     * $b = ['a' => 'A', 'b' => 'B', 'c' => 'C'];
     * $it = Iterables::parallel($a,$b);
     * print_r(\iterator_to_array($it));
     * ```
     * Displays
     * ```txt
     * Array
     * (
     *     [0] => 10
     *     [a] => A
     *     [1] => 20
     *     [b] => B
     *     [2] => 30
     *     [c] => C
     * )
     * ```
     * 
     * @template K
     * @template V
     * @param iterable<int,iterable<K,V>> $iterables The iterables to iterate through.
     * @param ParallelFlag $flag The flag to set.
     * @return Iterator<K,V> A parallel iterator.
     */
    public static function parallel(
        iterable $iterables,
        ParallelFlag $flag = ParallelFlag::NEED_ANY,
    ): Iterator {
        return new class($flag, $iterables) extends AbstractParallelIteratorOperation {
            protected function op(mixed $key, mixed $value): Iterator
            {
                return Iterables::combine($key, $value);
            }
        };
    }

    /**
     * Returns the chunks of a parallel iteration.
     * 
     * Example
     * ```php
     * $a = [10,20,30];
     * $b = ['a' => 'A', 'b' => 'B', 'c' => 'C'];
     * $it = Iterables::parallel($a,$b);
     * print_r(\iterator_to_array($it));
     * ```
     * Displays
     * ```txt
     * Array
     * (
     *     [0] => ArrayIterator Object
     *         (
     *             [storage:ArrayIterator:private] => Array
     *                 (
     *                     [0] => 10
     *                     [a] => A
     *                 )
     * 
     *         )
     * 
     *     [1] => ArrayIterator Object
     *         (
     *             [storage:ArrayIterator:private] => Array
     *                 (
     *                     [1] => 20
     *                     [b] => B
     *                 )
     * 
     *         )
     * 
     *     [2] => ArrayIterator Object
     *         (
     *             [storage:ArrayIterator:private] => Array
     *                 (
     *                     [2] => 30
     *                     [c] => C
     *                 )
     * 
     *         )
     * 
     * )
     * ```
     * 
     * @template K
     * @template V
     * @param iterable<int,iterable<K,V>> $iterables The iterables to iterate through.
     * @param ?Closure $makeChunk
     *  Returns the chunk to use as a result (default: {@see Iterables::combine}).
     *  - makeChunk(array $values, array $keys): mixed, or
     *  - makeChunk(Entry $keysValues): mixed
     * @param ParallelFlag $flag The flag to set.
     * @return Iterator<int,Iterator<K,V>> A parallel iterator.
     */
    public static function parallelChunk(
        iterable $iterables,
        ?Closure $makeChunk = null,
        ParallelFlag $flag = ParallelFlag::NEED_ANY,
    ): Iterator {
        $makeChunk ??= fn(array $v, array $k) => Iterables::combine($k, $v);

        return new class($flag, $iterables, $makeChunk) extends AbstractParallelIteratorOperation {
            public function __construct(
                $flag,
                $iterables,
                private $makeChunk
            ) {
                parent::__construct($flag, $iterables);
            }
            protected function op(mixed $keys, mixed $values): Entry
            {
                return new Entry($this->i, ($this->makeChunk)($values, $keys));
            }
        };
    }

    // ========================================================================

    /**
     * Get the first key.
     *
     * @template K
     * @param iterable<K,mixed> $iterable A sequence of entries.
     * @param mixed $default A default value to return.
     * @return K The first key of $iterable, or $default if the sequence is empty.
     */
    public static function firstKey(iterable $iterable, $default = null): mixed
    {
        if (\is_array($iterable))
            return Arrays::firstKey($iterable);

        foreach ($iterable as $k => $NotUsed)
            return $k;

        return $default;
    }

    /**
     * Get the first value.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable A sequence of entries.
     * @param mixed $default A default value to return.
     * @return V The first value of $iterable, or $default if the sequence is empty.
     */
    public static function firstValue(iterable $iterable, $default = null): mixed
    {
        if (\is_array($iterable))
            return Arrays::firstValue($iterable);

        foreach ($iterable as $v)
            return $v;

        return $default;
    }

    /**
     * Get the last key.
     *
     * @template K
     * @param iterable<K,mixed> $iterable A sequence of entries.
     * @param mixed $default A default value to return.
     * @return K The last key of $iterable, or $default if the sequence is empty.
     */
    public static function lastKey(iterable $iterable, $default = null): mixed
    {
        if (\is_array($iterable))
            return Arrays::lastKey($iterable);

        $k = $default;

        foreach ($iterable as $k => $NotUsed);
        return $k;
    }

    /**
     * Get the last value.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable A sequence of entries.
     * @param mixed $default A default value to return.
     * @return V The last value of $iterable, or $default if the sequence is empty.
     */
    public static function lastValue(iterable $iterable, $default = null): mixed
    {
        if (\is_array($iterable))
            return Arrays::lastValue($iterable);

        $v = $default;

        foreach ($iterable as $v);
        return $v;
    }

    /**
     * An iterator over the first iterable entry.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable An iterable to fetch from.
     *  If the iterable is an Iterator then it moves to the next entry.
     * @return Iterator<V> An iterator on the first entry.
     */
    public static function first(iterable $iterable): Iterator
    {
        if (\is_array($iterable))
            $e = Arrays::firstEntry($iterable);
        else {
            foreach ($iterable as $k => $v) {
                $e = new Entry($k, $v);
                break;
            }
            if ($iterable instanceof Iterator) {
                $iterable->next();
                $iterable->valid();
            }
        }
        if (!isset($e))
            return new EmptyIterator;

        return new EntryIterator($e);
    }


    /**
     * An iterator over the last array entry.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable An iterable to fetch from.
     * @return Iterator<V> An iterator on the first entry.
     */
    public static function last(iterable $iterable): Iterator
    {
        if (\is_array($iterable))
            $e = Arrays::lastEntry($iterable);
        else {
            foreach ($iterable as $k => $v);
            $e = isset($k) ?
                /* @phpstan-ignore variable.undefined */
                new Entry($k, $v)
                : null;
        }
        if (!isset($e))
            return new EmptyIterator;

        return new EntryIterator($e);
    }

    /**
     * Gets the first iterable entry.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable An iterable.
     * @param V $default A value to return if the iterable is empty.
     * @return ?Entry<K,V> The first entry.
     */
    public static function firstEntry(iterable $iterable, $default = null): ?Entry
    {
        if (\is_array($iterable))
            return Arrays::firstEntry($iterable) ?? $default;

        foreach ($iterable as $k => $v)
            return new Entry($k, $v);

        return $default;
    }

    /**
     * Gets the last iterable entry.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable An iterable.
     * @return ?Entry<K,V> The last entry.
     */
    public static function lastEntry(iterable $iterable, $default = null): ?Entry
    {
        if (\is_array($iterable))
            return Arrays::lastEntry($iterable) ?? $default;

        foreach ($iterable as $k => $v);

        if (isset($k))
            /* @phpstan-ignore variable.undefined */
            return new Entry($k, $v);
        else
            return $default;
    }

    // ========================================================================
    /**
     * Applies closures to each key and value from entries.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable An iterable to walk through.
     * @param Closure $mapKey A closure to apply on keys.
     *  - `mapKey(mixed $key):mixed`
     * @param Closure $mapValue A closure to apply on values.
     *  - `mapValue(mixed $value):mixed`
     * @return Iterator<mixed,mixed> An iterator on the mapped entries.
     */
    public static function map(
        iterable $iterable,
        Closure $mapKey,
        Closure $mapValue
    ): Iterator {
        return new class(
            Cast::iterableToIterator($iterable),
            $mapKey,
            $mapValue
        ) extends AbstractIteratorOperation {

            public function __construct(
                iterable $iterable,
                private Closure $mapKey,
                private Closure $mapValue
            ) {
                parent::__construct($iterable);
            }

            protected function op(mixed $key, mixed $value): Entry
            {
                return new Entry(($this->mapKey)($key), ($this->mapValue)($value));
            }
        };
    }

    /**
     * Applies a closure on each key.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable An iterable to walk through.
     * @param Closure $mapKey A closure to apply on keys.
     *  - `mapKey(mixed $key):mixed`
     * @return Iterator<mixed,V> An iterator on the mapped entries.
     */
    public static function mapKey(iterable $iterable, Closure $mapKey): Iterator
    {
        return new class(
            Cast::iterableToIterator($iterable),
            $mapKey
        ) extends AbstractIteratorOperation {

            public function __construct(
                iterable $iterable,
                private Closure $mapKey
            ) {
                parent::__construct($iterable);
            }

            protected function op(mixed $key, mixed $value): Entry
            {
                return new Entry(($this->mapKey)($key), $value);
            }
        };
    }

    /**
     * Applies a closure on each value.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable An iterable to walk through.
     * @param Closure $mapValue A closure to apply on values.
     *  - `mapValue(mixed $value):mixed`
     * @return iterable<K,mixed> An iterable over the mapped entries.
     *  If $iterable is an array then returns an array, otherwise returns an Iterator.
     */
    public static function mapValue(iterable $iterable, Closure $mapValue): iterable
    {
        if (\is_array($iterable))
            return \array_map($mapValue, $iterable);

        return new class(
            Cast::iterableToIterator($iterable),
            $mapValue
        ) extends AbstractIteratorOperation {

            public function __construct(
                iterable $iterable,
                private Closure $mapValue
            ) {
                parent::__construct($iterable);
            }

            protected function op(mixed $key, mixed $value): Entry
            {
                return new Entry($key, ($this->mapValue)($value));
            }
        };
    }

    // ========================================================================

    /**
     * An iterator over a slice of an iterable.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable An iterable to walk through.
     * @param int<0, max> $offset A positive offset from wich to begin.
     * @param ?int<0, max> $length A positive length of the number of entries to read.
     *      If set to null then all the entries are read from the offset.
     * @return iterable<K,V> An iterable over the selected slice.
     *  If $iterable is an array then returns an array, otherwise returns an Iterator.
     * 
     * @throws \DomainException If the offset or the length is negative.
     */
    public static function limit(iterable $iterable, int $offset = 0, ?int $length = null): iterable
    {
        if ($offset < 0)
            throw new \DomainException("The offset must be positive, has $offset");
        if ($length < 0)
            throw new \DomainException("The offset must be positive, has $length");

        if (\is_array($iterable)) {

            if ($length === 0)
                return [];
            else
                return \array_slice($iterable, $offset, $length, true);
        } elseif ($length === 0)
            return new \EmptyIterator();

        return new \LimitIterator(cast::iterableToIterator($iterable), $offset, $length ?? -1);
    }

    // ========================================================================

    /**
     * Finds entries from an iterable that are not in relation with any entry from another iterable.
     *
     * @template K
     * @template V
     * 
     * @param \Closure $searchRelations
     *  Finds whether an entry ($akey => $aval) of $a has a relation with an entry of $b.
     *  If there is a relation then the callback must return the keys of $b in relation with $aval,
     *  else it must return false.
     *  - searchRelations(K $akey, V $aval, iterable &$b):array<K>|K
     * @param iterable<K,V> $a The iterable to associate from.
     * @param iterable<mixed> $b The iterable to associate to.
     * @return \Iterator<K,V> Returns an \Iterator of ($k => $v) entries from $a without any relation with an entry of $b.
     */
    public static function findEntriesWithoutRelation(\Closure $searchRelations, iterable $a, iterable $b): \Iterator
    {
        foreach ($a as $k => $v) {
            if (false === $searchRelations($k, $v, $b))
                yield $k => $v;
        }
    }

    /**
     * Finds all relations between each entry from an iterable to entries from another iterable.
     *
     * @template K
     * @template V
     * 
     * @param \Closure $searchRelations
     *  Finds whether an entry ($akey => $aval) of $a has a relation with an entry of $b.
     *  If there is a relation then the callback must returns the keys of $b in relation with $aval,
     *  else it must returns false.
     *  - searchRelations(K $akey, V $aval, iterable &$b):array<K>|K
     * @param iterable<K,V> $a The iterable to associate from.
     * @param array<mixed> $b The iterable to associate to.
     * @return \Iterator<K,string|int> An \Iterator of ($ka => $kb) entries
     *  where $ka is from ($ka => $va) an entry of $a in relation
     *  and $kb is from ($kb => $vb) an entry of $b.
     */
    public static function findEntriesRelations(\Closure $searchRelations, iterable $a, iterable $b): \Iterator
    {
        foreach ($a as $k => $v) {
            $bkeys = $searchRelations($k, $v, $b);
            if (false === $bkeys)
                continue;
            foreach (Ensure::iterable($bkeys) as $bk)
                yield $k => $bk;
        }
    }

    // ========================================================================

    /**
     * Checks that an iterable has the same values as another (order independent).
     *
     * @param iterable<mixed> $a An iterable.
     * @param iterable<mixed> $b An iterable.
     * @param bool $strict If the comparison must be strict (===) or not (==).
     */
    public static function valuesEquals(iterable $a, iterable $b, bool $strict = false): bool
    {
        if (
            (\is_array($a) || $a instanceof \Countable)
            && (\is_array($b) || $b instanceof \Countable)
            && \count($a) !== \count($b)
        )
            return false;

        $diff = self::valuesInjectionDiff($a, $b, $strict);
        $diff->rewind();
        return !$diff->valid();
    }

    /**
     * Finds the values of an iterable $a that are not in an iterable $b.
     *
     * Each value of $b can at most be tagged once to be a value of $a.
     * For instance if $a=['a', 'a'] and $b=['a']
     * then the difference returns ['a'] because the second 'a' of $a
     * cannot be compared to the same 'a' as the previous comparison.
     * 
     * @param iterable<mixed> $a An iterable.
     * @param iterable<mixed> $b An iterable.
     * @param bool $strict If the comparison must be strict (===) or not (==).
     */
    public static function valuesInjectionDiff(iterable $a, iterable $b, bool $strict = false): \Iterator
    {
        $b = \iterator_to_array($b);
        return self::findEntriesWithoutRelation(
            function (string|int $akey, mixed $aval, array &$b) use ($strict): bool {
                $key =  \array_search($aval, $b, $strict);
                if (false === $key)
                    return false;
                unset($b[$key]);
                return true;
            },
            $a,
            $b
        );
    }

    // ========================================================================

    private static function sequenceSizeIsLowerThan_mayBeStrict(bool $strict): \Closure
    {
        return $strict ? self::sequenceSizeIsStrictlyLowerThan(...) : self::sequenceSizeIsLowerOrEqual(...);
    }

    /**
     * @param \Iterator<mixed> $a A sequence of entries.
     * @param \Iterator<mixed> $b A sequence of entries.
     */
    private static function sequenceSizeIsLowerOrEqual(\Iterator $a, \Iterator $b): bool
    {
        return !$a->valid();
    }

    /**
     * @param \Iterator<mixed> $a A sequence of entries.
     * @param \Iterator<mixed> $b A sequence of entries.
     */
    private static function sequenceSizeIsStrictlyLowerThan(\Iterator $a, \Iterator $b): bool
    {
        return !$a->valid() && $b->valid();
    }

    /**
     * @param \Iterator<mixed> $a A sequence of entries.
     * @param \Iterator<mixed> $b A sequence of entries.
     */
    private static function sequenceSizeEquals(\Iterator $a, \Iterator $b): bool
    {
        return !$a->valid() && !$b->valid();
    }

    private static function true(): \Closure
    {
        return fn() => true;
    }

    private static function equals_mayBeStrict(bool $strict): \Closure
    {
        return $strict ? Functions::areTheSame(...) : Functions::equals(...);
    }

    // ========================================================================

    /**
     * Ensure that an iterable is rewindable.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $sequence A sequence of entries.
     * @param bool $iteratorClassIsRewindable
     *      true if the sent $sequence is a rewindable iterable or a Generator.
     * @return Iterator<K,V> A rewindable iterator.
     */
    // TODO: is this function usefull ?
    private static function ensureRewindableIterator(iterable $sequence, bool $iteratorClassIsRewindable = true): Iterator
    {
        if (\is_array($sequence))
            return new ArrayIterator($sequence);
        if ($iteratorClassIsRewindable && $sequence instanceof Iterator) {

            if (!($sequence instanceof Generator))
                return $sequence;
        }
        return new ArrayIterator(\iterator_to_array($sequence));
    }

    /**
     * @param iterable<mixed,mixed> $a A sequence of entries.
     * @param iterable<mixed,mixed> $b A sequence of entries.
     */
    private static function sequenceHasInclusionRelation(iterable $a, iterable $b, \Closure $keyEquals, \Closure $valueEquals, \Closure $endValidation): bool
    {
        $a = Iterables::ensureRewindableIterator($a);
        $b = Iterables::ensureRewindableIterator($b);
        $a->rewind();
        $b->rewind();

        while ($a->valid() && $b->valid()) {

            if (!$keyEquals($a->key(), $b->key()) || !$valueEquals($a->current(), $b->current()))
                return false;

            $a->next();
            $b->next();
        }
        return $endValidation($a, $b);
    }

    // ========================================================================

    /**
     * Checks if two sequences are in an equal relation according to external keys and values comparison closures.
     *
     * Two sequences are in an equal relation if they have the same (key => value) entries in the same order.
     *
     * @param iterable<mixed,mixed> $a A sequence of entries.
     * @param iterable<mixed,mixed> $b A sequence of entries.
     * @param \Closure $keyEquals The keys comparison closure.
     * @param \Closure $valueEquals The values comparison closure.
     * @return bool true if there is an equal relation between the sequences, or else false.
     */
    public static function sequenceHasEqualRelation(iterable $a, iterable $b, \Closure $keyEquals, \Closure $valueEquals): bool
    {
        return self::sequenceHasInclusionRelation($a, $b, $keyEquals, $valueEquals, self::sequenceSizeEquals(...));
    }

    /**
     * Checks if two sequences are equals using one of the php equal operator (== or ===) as keys and values comparison.
     *
     * Two sequences are equals if they have the same key => value entries in the same order.
     *
     * @param iterable<mixed,mixed> $a A sequence of entries.
     * @param iterable<mixed,mixed> $b A sequence of entries.
     * @param bool $strictKeyEquals true if the keys comparison is ===, or false for ==.
     * @param bool $strictValueEquals true if the values comparison is ===, or false for ==.
     * @return bool true if the sequences are equals, or else false.
     */
    public static function sequenceEquals(iterable $a, iterable $b, bool $strictKeyEquals = false, bool $strictValueEquals = false): bool
    {
        return self::sequenceHasEqualRelation($a, $b, self::equals_mayBeStrict($strictKeyEquals), self::equals_mayBeStrict($strictValueEquals));
    }

    /**
     * Checks if a sequence is the begining of another one according to external keys and values comparison closures.
     *
     * @param iterable<mixed,mixed> $a The first sequence of entries.
     * @param iterable<mixed,mixed> $b The second sequence of entries.
     * @param \Closure $keyEquals The keys comparison closure.
     * @param \Closure $valueEquals The values comparison closure.
     * @param bool $strictPrefix true if the first sequence must be smaller than the second, or false if both may have the same size.
     * @return bool true if the first sequence is a prefix of the second one, or else false.
     */
    public static function sequenceHasPrefixRelation(iterable $a, iterable $b, \Closure $keyEquals, \Closure $valueEquals, bool $strictPrefix = false): bool
    {
        return self::sequenceHasInclusionRelation($a, $b, $keyEquals, $valueEquals, self::sequenceSizeIsLowerThan_mayBeStrict($strictPrefix));
    }

    /**
     * Checks if a sequence is the begining of another using one of the php equal operator (== or ===) as keys and values comparison.
     *
     * @param iterable<mixed,mixed> $a The first sequence of entries.
     * @param iterable<mixed,mixed> $b The second sequence of entries.
     * @param bool $strictKeyEquals true if the keys comparison is ===, or false for ==.
     * @param bool $strictValueEquals true if the values comparison is ===, or false for ==.
     * @param bool $strictPrefix true if the first sequence must be smaller than the second, or false if both may have the same size.
     * @return bool true if the first sequence is a prefix of the second one, or else false.
     */
    public static function sequencePrefixEquals(iterable $a, iterable $b, bool $strictKeyEquals = false, $strictValueEquals = false, bool $strictPrefix = false): bool
    {
        return self::sequenceHasPrefixRelation($a, $b, self::equals_mayBeStrict($strictKeyEquals), self::equals_mayBeStrict($strictValueEquals), $strictPrefix);
    }

    // ========================================================================

    /**
     * Checks if two lists are in an equal relation according to an external values comparison closure.
     *
     * Two lists are in an equal relation if they have the same values in the same order.
     *
     * @param iterable<mixed,mixed> $a A list of values.
     * @param iterable<mixed,mixed> $b A list of values.
     * @param \Closure $valueEquals The values comparison closure.
     * @return bool true if there is an equal relation between the lists, or else false.
     */
    public static function listHasEqualRelation(iterable $a, iterable $b, \Closure $valueEquals): bool
    {
        return self::sequenceHasInclusionRelation($a, $b, self::true(), $valueEquals, self::sequenceSizeEquals(...));
    }

    /**
     * Checks if two lists are in an equal relation using one of the php equal operator (== or ===) as values comparison.
     *
     * Two lists are in an equal relation if they have the same values in the same order.
     *
     * @param iterable<mixed,mixed> $a A list of values.
     * @param iterable<mixed,mixed> $b A list of values.
     * @param bool $strictEquals true if the values comparison is ===, or false for ==.
     * @return bool true if the lists are equals, or else false.
     */
    public static function listEquals(iterable $a, iterable $b, bool $strictEquals = false): bool
    {
        return self::listHasEqualRelation($a, $b, self::equals_mayBeStrict($strictEquals));
    }

    /**
     * Checks if a list is the begining of another one according to an external values comparison closure.
     *
     * @param iterable<mixed,mixed> $a The first list of values.
     * @param iterable<mixed,mixed> $b The second list of values.
     * @param \Closure $valueEquals The values comparison closure.
     * @param bool $strictPrefix true if the first list must be smaller than the second, or false if both may have the same size.
     * @return bool true if the first list is a prefix of the second one, or else false.
     */
    public static function listHasPrefixRelation(iterable $a, iterable $b, \Closure $valueEquals, bool $strictPrefix = false): bool
    {
        return self::sequenceHasInclusionRelation($a, $b, self::true(), $valueEquals, self::sequenceSizeIsLowerThan_mayBeStrict($strictPrefix));
    }

    /**
     * Checks if a list is the begining of another one using one of the php equal operator (== or ===) as values comparison.
     *
     * @param iterable<mixed,mixed> $a The first list of values.
     * @param iterable<mixed,mixed> $b The second list of values.
     * @param bool $strictEquals true if the values comparison is ===, or false for ==.
     * @param bool $strictPrefix true if the first list must be smaller than the second, or false if both may have the same size.
     * @return bool true if the first list is a prefix of the second one, or else false.
     */
    public static function listPrefixEquals(iterable $a, iterable $b, bool $strictEquals = false, bool $strictPrefix = false): bool
    {
        return self::listHasPrefixRelation($a, $b, self::equals_mayBeStrict($strictEquals), $strictPrefix);
    }
    // ========================================================================

    /**
     * Cartesian product between iterables calling a closure to make a result entry.
     *
     * Note that a cartesian product has no result if an iterable is empty.
     * 
     * @template K
     * @template V
     * 
     * @param Closure $makeEntry
     *  The closure to make a result entry.
     *  It must returns a R value representing a selected iterable entry ($k => $v).
     *  - $makeEntry(K $k, V $v):R
     * @param iterable<K,V> ...$arrays
     *            A sequence of iterable.
     * @return Generator<list<mixed>> An iterator of lists of $makeEntry($k_i, $v_i):
     *  - [ $makeEntry(k_1, v_1), ... ,$makeEntry($k_i, $v_i) ]
     * 
     *  where ($k_i => $v_i) is an entry from the i^th iterator.
     */
    public static function cartesianProductMakeEntries(Closure $makeEntry, iterable ...$arrays): Generator
    {
        if (empty($arrays))
            return [];

        foreach ($arrays as $a) {
            $it = Iterables::ensureRewindableIterator($a);
            $keys[] = $it;
            $it->rewind();

            if (!$it->valid())
                return [];

            $result[] = [
                $it->key() => $it->current()
            ];
            $it->next();
        }
        yield $result;

        loop:
        $i = \count($arrays);
        while ($i--) {
            $it = $keys[$i];

            if (!$it->valid()) {
                $it->rewind();
                $result[$i] = $makeEntry($it->key(), $it->current());
                $it->next();
            } else {
                $result[$i] = $makeEntry($it->key(), $it->current());
                $it->next();
                yield $result;
                goto loop;
            }
        }
    }

    // ========================================================================

    /**
     * Cartesian product between iterables;
     * each selected entry ($k_i => $v_i) of an iterable
     * is returned as an array [$k_i => $v_i].
     *
     * Note that a cartesian product has no result if an iterable is empty.
     * 
     * @template V
     * @param iterable<V> ...$arrays
     *            A sequence of iterable.
     * @return Generator<int,list<V[]>>
     *  An iterator of list of  [$k_i => $v_i] pairs:
     *  - [ [k_1 => v_1], ... , [$k_i => $v_i] ]
     * 
     *  where ($k_i => $v_i) is an entry from the i^th iterator.
     */
    public static function cartesianProduct(iterable ...$arrays): Generator
    {
        /** @var Iterator<int,array<int,V[]>> */
        return Iterables::cartesianProductMakeEntries(fn($k, $v) => [$k => $v], ...$arrays);
    }

    /**
     * Cartesian product between iterables;
     * each selected entry ($k_i => $v_i) of an iterable
     * is returned as an array pair [$k_i, $v_i].
     *
     *  Note that a cartesian product has no result if an iterable is empty.
     * 
     * @template V
     * @param iterable<V> ...$arrays
     *            A sequence of iterable.
     * @return Generator<int,list<list<mixed>>>
     *  An iterator of list of [$k_i, $v_i] pairs:
     *  - [ [k_1, v_1], ... , [$k_i, $v_i] ]
     * 
     *  where ($k_i => $v_i) is an entry from the i^th iterator.
     */
    public static function cartesianProductPairs(iterable ...$arrays): Generator
    {
        /** @var Iterator<int,array<int,array<int,mixed>>> */
        return Iterables::cartesianProductMakeEntries(fn($k, $v) => [$k, $v], ...$arrays);
    }

    /**
     * Cartesian product between iterables;
     * all selected entries ($k_i => $v_i) of the iterables
     * are merged into a single array in the result.
     *
     *  Note that a cartesian product has no result if an iterable is empty.
     * 
     * @template V
     * @param iterable<V> ...$arrays
     *            A sequence of iterable.
     * @return Generator<int,V[]>
     *  An iterator of array:
     * - [k_1 => v_1, ... , $k_i => $v_i]
     * 
     *  where ($k_i => $v_i) is an entry from the i^th iterator.
     */
    public static function cartesianProductMerger(iterable ...$arrays): Generator
    {
        return self::mergeCartesianProduct(
            self::cartesianProduct(...$arrays)
        );
    }

    /**
     * Transform each result of a cartesianProduct() iterator into a simple array of all its pair entries.
     *
     * @template V
     * @param Iterator<list<V[]>> $cartesianProduct
     *            The iterator of a cartesian product.
     * @return Generator<int,V[]> An Iterator of flat array which correspond to the merging of all its pairs [$k_i => $v_i].
     */
    private static function mergeCartesianProduct(Iterator $cartesianProduct): Generator
    {
        foreach ($cartesianProduct as $result)
            yield \array_merge(...$result);
    }

    // ========================================================================
}
