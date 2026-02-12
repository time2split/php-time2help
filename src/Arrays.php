<?php

declare(strict_types=1);

namespace Time2Split\Help;

use Closure;
use Time2Split\Help\Container\Entry;

/**
 * Functions on arrays.
 * 
 * @package time2help\container
 * @author Olivier Rodriguez (zuri)
 */
final class Arrays
{
    use Classes\NotInstanciable;

    private static function nullValue(): object
    {
        static $isNull = new \stdClass();
        return $isNull;
    }

    /**
     * Gets the first entry.
     * 
     * @param array $array An array.
     * @return ?Entry
     *  The first entry,
     *  or `null` if the array is empty.
     * 
     * @template K of array-key
     * @template V
     * @phpstan-param array<K,V> $array
     * @phpstan-return ?Entry<K,V>
     */
    public static function firstEntry(array $array): ?Entry
    {
        if (empty($array))
            return null;

        $k = \array_key_first($array);
        return new Entry($k, $array[$k]);
    }

    /**
     * Gets the last entry.
     * 
     * @param array $array An array.
     * @return ?Entry
     *  The last entry,
     *  or `null` if the array is empty.
     * 
     * @template K of array-key
     * @template V
     * @phpstan-param array<K,V> $array
     * @phpstan-return ?Entry<K,V>
     */
    public static function lastEntry(array $array): ?Entry
    {
        if (empty($array))
            return null;

        $k = \array_key_last($array);
        return new Entry($k, $array[$k]);
    }

    // ========================================================================
    // Optional versions

    /**
     * Gets the first key.
     * 
     * @param array $array An array.
     * @return Optional
     *  The first key
     *  (an empty optional if the array is empty).
     * 
     * @template K of array-key
     * 
     * @phpstan-param array<K,mixed> $array
     * @phpstan-return Optional<K>
     */
    public static function firstKeyOpt(array $array): Optional
    {
        $null = Arrays::nullValue();
        return Optional::ofNullable(Arrays::firstKey($array, $null), $null);
    }

    /**
     * Gets the first value.
     * 
     * @param array $array An array.
     * @return Optional
     *  The first value
     *  (an empty optional if the array is empty).
     * 
     * @template V
     * 
     * @phpstan-param array<V> $array
     * @phpstan-return Optional<V>
     */
    public static function firstValueOpt(array $array): Optional
    {
        $null = Arrays::nullValue();
        /** @var Optional<V> */
        return Optional::ofNullable(Arrays::firstValue($array, $null), $null);
    }

    /**
     * Gets the last key.
     * 
     * @param array $array An array.
     * @return Optional
     *  The last key
     *  (an empty optional if the array is empty).
     * 
     * @template K of array-key
     * 
     * @phpstan-param array<K,mixed> $array
     * @phpstan-return Optional<K>
     */
    public static function lastKeyOpt(array $array): Optional
    {
        $null = Arrays::nullValue();
        return Optional::ofNullable(Arrays::lastKey($array, $null), $null);
    }

    /**
     * Gets the last value.
     * 
     * @param array $array An array.
     * @return Optional
     *  The last value
     *  (an empty optional if the array is empty).
     * 
     * @template V
     * 
     * @phpstan-param array<V> $array
     * @phpstan-return Optional<V>
     */
    public static function lastValueOpt(array $array): Optional
    {
        $null = Arrays::nullValue();
        /** @var Optional<V> */
        return Optional::ofNullable(Arrays::lastValue($array, $null), $null);
    }

    // ========================================================================

    /**
     * Gets the first key.
     * 
     * @param array $array An array.
     * @param mixed $default A default value.
     * @return string|int|mixed
     *  The first key,
     *  or `$default` if the array is empty.
     * 
     * @template K of array-key
     * @template D
     * 
     * @phpstan-param array<K,mixed> $array
     * @phpstan-param D $default
     * @phpstan-return ($array is non-empty-array ? K : D)
     */
    public static function firstKey(array $array, mixed $default = null): mixed
    {
        if (empty($array))
            return $default;

        return \array_key_first($array);
    }

    /**
     * Gets the first value.
     * 
     * @param array $array An array.
     * @param mixed $default A default value.
     * @return mixed
     *  The first value,
     *  or `$default` if the array is empty.
     * 
     * @template V
     * @template D
     * 
     * @phpstan-param array<V> $array
     * @phpstan-param D $default
     * @phpstan-return ($array is non-empty-array ? V : D)
     */
    public static function firstValue(array $array, mixed $default = null): mixed
    {
        if (empty($array))
            return $default;

        return $array[\array_key_first($array)];
    }

    /**
     * Gets the last key.
     * 
     * @param array $array An array.
     * @param mixed $default A default value.
     * @return string|int|mixed
     *  The last key,
     *  or `$default` if the array is empty.
     * 
     * @template K of array-key
     * @template D
     * 
     * @phpstan-param array<K,mixed> $array
     * @phpstan-param D $default
     * @phpstan-return ($array is non-empty-array ? K : D)
     */
    public static function lastKey(array $array, mixed $default = null): mixed
    {
        if (empty($array))
            return $default;

        return \array_key_last($array);
    }

    /**
     * Gets the last value.
     * 
     * @param array $array An array.
     * @param mixed $default A default value.
     * @return mixed
     *  The last value,
     *  or `$default` if the array is empty.
     * 
     * @template V
     * @template D
     * 
     * @phpstan-param array<V> $array
     * @phpstan-param D $default
     * @phpstan-return ($array is non-empty-array ? V : D)
     */
    public static function lastValue(array $array, mixed $default = null): mixed
    {
        if (empty($array))
            return $default;

        return $array[\array_key_last($array)];
    }

    /**
     * Gets an entry value from its key.
     * 
     * @param array<mixed> $array
     *      An array.
     * @param string|int $key
     *      The key of the entry to get.
     * @deprecated
     *      Replaced by {@see Arrays::value()}.
     * 
     * @template K of array-key
     * @template V
     * 
     * @phpstan-param array<K,V> $array
     * @phpstan-param K $key
     * @phpstan-return Optional<V>
     */
    public static function getValueIfKeyExists(array $array, string|int $key): Optional
    {
        return Arrays::value($array, $key);
    }

    /**
     * Gets an entry value from its key.
     * 
     * @param array $array
     *      An array.
     * @param string|int $key
     *      The key of the entry to get.
     * @return Optional
     *      The value of the entry.
     * 
     * @template K of array-key
     * @template V
     * 
     * @phpstan-param array<K,V> $array
     * @phpstan-param K $key
     * @phpstan-return Optional<V>
     */
    public static function value(array $array, string|int $key): Optional
    {
        if (!\array_key_exists($key, $array))
            return Optional::empty();

        return Optional::of($array[$key]);
    }

    /**
     * Gets an entry from its key.
     * 
     * @param array<mixed> $array
     *      An array.
     * @param string|int $key
     *      The key of the entry to fetch.
     * @return ?Entry
     *      The entry
     *      or `null` if it not exists.
     * 
     * @template K of array-key
     * @template V
     * 
     * @phpstan-param array<K,V> $array
     * @phpstan-param K $key
     * @phpstan-return Entry<K,V>
     */
    public static function entry(array $array, string|int $key): ?Entry
    {
        if (!\array_key_exists($key, $array))
            return null;

        return new Entry($key, $array[$key]);
    }

    /**
     * Gets an entry from its position.
     * 
     * @param array<mixed> $array
     *      An array.
     * @param int $position
     *  The position of the entry to fetch.
     *   - If offset is non-negative, the sequence will start at that offset in the array.
     *   - If offset is negative, the sequence will start that far from the end of the array.
     * 
     * (Note: The offset parameter denotes the position in the array, not the key.)
     * 
     * @return ?Entry
     *      The entry
     *      or `null` if it not exists.
     * 
     * @template K of array-key
     * @template V
     * 
     * @phpstan-param array<K,V> $array
     * @phpstan-return Entry<K,V>
     */
    public static function entryAtPosition(array $array, int $position): ?Entry
    {
        $entry = \array_slice($array, $position, 1);

        foreach ($entry as $key => $value)
            return new Entry($key, $value);

        return null;
    }

    /**
     * Whether an entry exists.
     * 
     * @param array $array
     *      An array.
     * @param string|int $key
     *      The key of the entry.
     * @param mixed $value
     *      The value of the entry.
     * @param \Closure $sameValues
     *      - `true`:
     *          uses {@see Functions::areTheSame()}
     *      - `false`:
     *          uses {@see Functions::equals()}
     *      - `$valueEquals(mixed $a, mixed $b):bool`\
     *          Whether two values are equals.
     * @return bool
     *      `true` if the entry (key and value) exists in the array,
     *      `false` elsewhere.
     * 
     * @template K of array-key
     * @template V
     * 
     * @phpstan-param array<K,V> $array
     * @phpstan-param K $key
     * @phpstan-param V $value
     * @phpstan-param bool|\Closure(V $a, V $b):bool $sameValues
     */
    public static function entryExists(
        array $array,
        string|int $key,
        mixed $value,
        bool|\Closure $sameValues = false
    ): bool {

        if (!\array_key_exists($key, $array))
            return false;

        if (\is_bool($sameValues))
            $sameValues = Functions::getCallbackForEquals($sameValues);

        return $sameValues($value, $array[$value]);
    }

    // ========================================================================

    /**
     * Sets the first value.
     * 
     * If the array is empty it sets nothing.
     * 
     * @param array &$array A reference to an array to update.
     * @param mixed $value The new value for the entry.
     * @return Optional
     *      The previous value
     *      (an empty optional if the array is empty).
     * 
     * @template V
     * 
     * @phpstan-param array<V> &$array
     * @phpstan-param V $value
     * 
     * @phpstan-return Optional<V>
     */
    public static function setFirstValue(array &$array, mixed $value): Optional
    {
        if (empty($array))
            return Optional::empty();

        $k = Arrays::firstKey($array);
        $prev = $array[$k];
        $array[$k] = $value;
        return Optional::of($prev);
    }

    /**
     * Sets the last value.
     * 
     * If the array is empty it sets nothing.
     * 
     * @param array &$array A reference to an array to update.
     * @param mixed $value The new value for the entry.
     * @return Optional
     *      The previous value
     *      (an empty optional if the array is empty).
     * 
     * @template V
     * 
     * @phpstan-param array<V> &$array
     * @phpstan-param V $value
     * 
     * @phpstan-return Optional<V>
     */
    public static function setLastValue(array &$array, mixed $value): Optional
    {
        if (empty($array))
            return Optional::empty();

        $k = Arrays::lastKey($array);
        $prev = $array[$k];
        $array[$k] = $value;
        return Optional::of($prev);
    }

    /**
     * Sets the first key.
     * 
     * - If the array is empty it sets nothing.
     * - If the key already exists then
     *  the existant entry is removed from the array and
     *  the variable `$entry_out` is set with this entry.
     * 
     * @param array &$array A reference to an array to update.
     * @param string|int $key The new key for the entry.
     * @param ?Entry $entry_out 
     *      If the key is already present in the array then the variable
     *      is set to this existant entry.
     * @return Optional
     *      The previous key
     *      (an empty optional if the array is empty).
     * 
     * @template K of string|int
     * @template V
     * 
     * @phpstan-param array<K,V> &$array
     * @phpstan-param K $key
     * @phpstan-param Entry<K,V> $entry_out
     * 
     * @phpstan-return Optional<K>
     */
    public static function setFirstKey(
        array &$array,
        string|int $key,
        ?Entry &$entry_out = null
    ): Optional {
        if (empty($array))
            return Optional::empty();

        $k = Arrays::firstKey($array);
        $value = $array[$k];
        $entry_out = Arrays::entry($array, $key);

        // $key is not already the first one
        if ($k !== $key) {
            \array_shift($array);
            unset($array[$key]);
            $array = [$key => $value, ...$array];
        }
        return Optional::of($k);
    }

    /**
     * Sets the last key.
     * 
     * - If the array is empty it sets nothing.
     * - If the key already exists then
     *  the existant entry is removed from the array and
     *  the variable `$entry_out` is set with this entry.
     * 
     * @param array &$array A reference to an array to update.
     * @param string|int $key The new key for the entry.
     * @param ?Entry $entry_out 
     *      If the key is already present in the array then the variable
     *      is set to this existant entry.
     * @return Optional
     *      The previous key
     *      (an empty optional if the array is empty).
     * 
     * @template K of string|int
     * @template V
     * 
     * @phpstan-param array<K,V> &$array
     * @phpstan-param K $key
     * @phpstan-param Entry<K,V> $entry_out
     * 
     * @phpstan-return Optional<K>
     */
    public static function setLastKey(
        array &$array,
        string|int $key,
        ?Entry &$entry_out = null
    ): Optional {
        if (empty($array))
            return Optional::empty();

        $k = Arrays::lastKey($array);
        $value = $array[$k];
        $entry_out = Arrays::entry($array, $key);

        // $key is not already the last one
        if ($k !== $key) {
            \array_pop($array);
            unset($array[$key]);
            $array[$key] = $value;
        }
        return Optional::of($k);
    }

    // ========================================================================

    /**
     * Selects a part of an array.
     * 
     * @param array $array
     *      An array.
     * @param (string|int)[] $keys
     *      The keys from `$array` to select.
     * @param D $default
     *      A default value used as a placeholder for the non-existant entries.
     * 
     * @return array
     *  The entries of `$array` of the form (`$k => $v`) where `$k` belongs to `$keys`,
     *  or (`$k => $default`) if `$k` is not a key of `$array`.
     * 
     * @template K of array-key
     * @template V
     * @template D
     * 
     * @phpstan-param array<K,V> $array
     * @phpstan-param list<string|int> $keys
     * @phpstan-param D $default
     * @phpstan-return array<D|V>
     * 
     * @deprecated Replaced by {@see Arrays::select()}
     */
    public static function arraySelect(array $array, iterable $keys, $default = null): array
    {
        return Arrays::select($array, $keys, $default);
    }

    /**
     * Selects a part of an array.
     * 
     * @param array $array
     *      An array.
     * @param (string|int)[] $keys
     *      The keys from `$array` to select.
     * @param D $default
     *      A default value used as a placeholder for the non-existant entries.
     * 
     * @return array
     *  The entries of `$array` of the form (`$k => $v`) where `$k` belongs to `$keys`,
     *  or (`$k => $default`) if `$k` is not a key of `$array`.
     * 
     * @template K of array-key
     * @template V
     * @template D
     * 
     * @phpstan-param array<K,V> $array
     * @phpstan-param list<string|int> $keys
     * @phpstan-param D $default
     * @phpstan-return array<D|V>
     */
    public static function select(array $array, iterable $keys, $default = null): array
    {
        $ret = [];

        foreach ($keys as $k)
            $ret[$k] = $array[$k] ?? $default;

        return $ret;
    }

    // ========================================================================

    /**
     * Applies a mapping to the keys of a given array.
     * 
     * Each entry (`$k => $v`) is replaced by (`$map($k) => $v`).
     * 
     * @param array &$array
     *      A reference to an array to update.
     * @param Closure $map
     *      A closure to run for each key of the array.
     *       - `$map(string|int $key):string|int`
     * 
     * @template K of array-key
     * @template V
     * 
     * @phpstan-param array<K,V> &$array
     * @phpstan-param Closure(K $key):K $map
     */
    public static function mapKey(array &$array, Closure $map): void
    {
        $array = \array_combine(\array_map($map, \array_keys($array)), $array);
    }

    /**
     * Applies a mapping to the values of a given array.
     * 
     * Each entry (`$k => $v`) is replaced by (`$k => $map($v)`).
     * 
     * @param array &$array
     *      A reference to an array to update.
     * @param Closure $map
     *      A closure to run for each value of the array.
     *       - `$map(mixed $value):mixed`
     * 
     * @template K of array-key
     * @template V
     * 
     * @phpstan-param array<K,V> &$array
     * @phpstan-param Closure(V $value):V $map
     */
    public static function mapValue(array &$array, Closure $map): void
    {
        foreach ($array as &$v)
            $v = $map($v);
    }

    /**
     * Applies a mapping to the entries of a given array.
     * 
     * Each entry (`$k => $v`) is replaced by (`$e->key => $e->value`)
     * where `$e = $map($k,$v)`.
     * 
     * @param array &$array
     *      A reference to an array to update.
     * @param Closure $map
     *      A closure to run for each value of the array.
     *       - `$map(string|int $key, mixed $value):Entry`
     * 
     * @template K of array-key
     * @template V
     * 
     * @phpstan-param array<K,V> &$array
     * @phpstan-param Closure(K $key, V $value):Entry<K,V> $map
     */
    public static function mapEntry(array &$array, Closure $map): void
    {
        $cp = $array;
        $array = [];

        foreach ($cp as $k => $v) {
            $entry = $map($k, $v);
            /** @phpstan-ignore parameterByRef.type */
            $array[$entry->key] = $entry->value;
        }
    }

    // ========================================================================

    /**
     * Maps then merges.
     * 
     * @param Closure $callback
     * A callable to run for each value in each array.
     *  - `$callback($value):mixed`
     * 
     * @param mixed[] $array An array to run through the callback function.
     * @param mixed[] ...$arrays
     *  Supplementary variable list of array arguments to run through the callback function.
     * 
     * @return mixed[] `\array_merge(...\array_map($callback, $array, ...$arrays))`
     * 
     * @link https://www.php.net/manual/en/function.array-map.php array_map()
     * @link https://www.php.net/manual/en/function.array-merge.php array_merge()
     */
    public static function arrayMapMerge(Closure $callback, array $array, array ...$arrays): array
    {
        return \array_merge(...\array_map($callback, $array, ...$arrays));
    }

    /**
     * Maps then deduplicates elements.
     * 
     * @param Closure $callback A callable to run for each value in each array.
     *  - `$callback($value):mixed`
     * 
     * @param mixed[] $array An array to run through the callback function.
     * @param int $flags 
     * The optional second parameter flags may be used to modify the comparison behavior using these values:
     * 
     * Comparison type flags:
     * - `SORT_REGULAR` - compare items normally (don't change types)
     * - `SORT_NUMERIC` - compare items numerically
     * - `SORT_STRING` - compare items as strings
     * - `SORT_LOCALE_STRING` - compare items as strings, based on the current locale.
     * 
     * @return mixed[] `\array_unique(\array_map($callback, $array), $flags)`
     * 
     * @link https://www.php.net/manual/en/function.array-map.php array_map()
     * @link https://www.php.net/manual/en/function.array-unique.php array_unique()
     */
    public static function arrayMapUnique(Closure $callback, array $array, int $flags = SORT_REGULAR): array
    {
        return \array_unique(\array_map($callback, $array), $flags);
    }

    /**
     * Applies a mapping to the keys of a given array.
     * 
     * @param Closure $map
     *      A closure to run for each key of the array.
     *       - `$map($key):string|int`
     * @param array $array
     *      An array.
     * @return array
     *      An array where each entry (`$k => $v`) has been replaced by (`$callback($k) => $v`).
     * 
     * @template K
     * @template V
     * @template M
     * 
     * @phpstan-param Closure(V $value):M $map
     * @phpstan-param array<K,V> $array
     * @phpstan-return array<M,V>
     * 
     * @deprecated Replaced by {@see Arrays::mapKey()}
     */
    public static function arrayMapKey(Closure $map, array $array): array
    {
        return \array_combine(\array_map($map, \array_keys($array)), $array);
    }

    /**
     * Partitions an array in two partitions according to a filter.
     * 
     * @template V
     * 
     * @param V[] $array An array.
     * @param Closure $filter A filter to apply on each entry of the array.
     *  If no callback is supplied, all empty entries of array will be removed.
     *  See `empty()` to know how PHP defines the empty semantic in this case.
     *  - `$filter(V $value):bool` (`$mode=0`)
     *  - `$filter(string|int $key):bool` (`$mode=ARRAY_FILTER_USE_KEY`)
     *  - `$filter(V $value, string|int $key):bool` (`$mode=ARRAY_FILTER_USE_BOTH`)
     * @param int $mode Flag determining what arguments are sent to callback:
     *  - `ARRAY_FILTER_USE_KEY` - pass key as the only argument to callback instead of the value
     *  - `ARRAY_FILTER_USE_BOTH` - pass both value and key as arguments to callback instead of the value
     *
     * Default is 0 which will pass value as the only argument to callback instead.
     * @return array<int,V[]> A list of two arrays where `$list[0]` are the entries validated by the filter
     *  and `$list[1]` are the remaining entries not filtered.
     * 
     * @link https://www.php.net/manual/fr/function.empty.php empty()
     */
    public static function arrayPartition(array $array, ?Closure $filter, int $mode = 0): array
    {
        $a = \array_filter($array, $filter, $mode);
        $b = \array_diff_key($array, $a);
        return [
            $a,
            $b
        ];
    }

    // ========================================================================
    // UPDATE
    // ========================================================================

    /**
     * Updates some entries in an array using callbacks.
     * 
     * @template K
     * @template V
     * 
     * @param array<K,V> &$array A reference to an array to update.
     * @param iterable<K,V> $update The (`$k => $v`) entries to set in the array.
     * @param ?Closure(K, V, array<K,V>&$a):void $onExists
     *  - `$onExists(string|int $k, U $v, V[] &$array):void`
     * 
     *  Updates an existant entry in array.
     *  If null then an `\Exception` is thrown for the first existant key entry met.
     * @param ?Closure(K, V, array<K,V>&$a):void $onUnexists
     *  - `$onUnexists(string|int $k, U $v, V[] &$array):void`
     * 
     *  Updates a non existant entry in array.
     *  If null then an `\Exception` is thrown for the first unexistant key entry met.
     */
    public static function updateWithClosures(
        array &$array,
        iterable $update,
        ?Closure $onExists = null,
        ?Closure $onUnexists = null
    ): void {
        if ($onUnexists === null)
            $onUnexists = fn($k, $v, $array) => throw new \Exception("The key '$k' does not exists in the array: " . implode(',', \array_keys($array)));
        if ($onExists === null)
            $onExists = fn($k, $v, $array) => throw new \Exception("The key '$k' already exists in the array: " . implode(',', \array_keys($array)));
        foreach ($update as $k => $v) {

            if (!\array_key_exists($k, $array))
                $onUnexists($k, $v, $array);
            else
                $onExists($k, $v, $array);
        }
    }

    /**
     * @param mixed[] $array
     */
    private static function updateEntry(string|int $k, mixed $v, array &$array): void
    {
        $array[$k] = $v;
    }

    /**
     * Sets the first entry of an array.
     * 
     * - If the array is empty it sets nothing.
     * - If the key already exists then
     *  the existant entry is removed from the array and
     *  the variable `$entry_out` is set with this entry.
     * 
     * @param array &$array A reference to an array to update.
     * @param string|int $key The new key for the entry.
     * @param mixed $value The new value for the entry.
     * @param ?Entry &$entry_out
     *      If the key is already present in the array then
     *      this variable is set to the existant entry.
     * @return ?Entry
     *      The previous first entry,
     *      or `null` if the array is empty.
     * 
     * @template K of array-key
     * @template V
     * 
     * @phpstan-param array<K,V> &$array
     * @phpstan-param K $key
     * @phpstan-param V $value
     * @phpstan-param ?Entry<K,V> &$entry_out
     * @phpstan-return ($array is non-empty-array ? Entry<K,V> : null)
     */
    public static function setFirstEntry(
        array &$array,
        string|int $key,
        mixed $value,
        ?Entry &$entry_out = null
    ): ?Entry {

        if (empty($array))
            return null;

        $k = \array_key_first($array);
        $entry_out = Arrays::entry($array, $key);

        if ($k !== $key) {
            $ret = new Entry($k, $array[$k]);
            \array_shift($array);
            unset($array[$key]);
            $array = [$key => $value, ...$array];
            return $ret;
        } else {
            assert($entry_out !== null);
            // The key corresponds already to the first entry
            $array[$key] = $value;
            return $entry_out;
        }
    }

    /**
     * Sets the last entry of an array.
     * 
     * - If the array is empty it sets nothing.
     * - If the key already exists then
     *  the existant entry is removed from the array and
     *  the variable `$entry_out` is set with this entry.
     * 
     * @param array &$array A reference to an array to update.
     * @param string|int $key The new key for the entry.
     * @param mixed $value The new value for the entry.
     * @param ?Entry &$entry_out
     *      If the key is already present in the array then
     *      this variable is set to the existant entry.
     * @return ?Entry
     *      The previous last entry,
     *      or `null` if the array is empty.
     * 
     * @template K of array-key
     * @template V
     * 
     * @phpstan-param array<K,V> &$array
     * @phpstan-param K $key
     * @phpstan-param V $value
     * @phpstan-param ?Entry<K,V> &$entry_out
     * @phpstan-return ($array is non-empty-array ? Entry<K,V> : null)
     */
    public static function setLastEntry(
        array &$array,
        string|int $key,
        mixed $value,
        ?Entry &$entry_out = null
    ): ?Entry {
        if (empty($array))
            return null;

        $k = \array_key_last($array);
        $entry_out = Arrays::entry($array, $key);

        if ($k !== $key) {
            $ret = new Entry($k, $array[$k]);
            \array_pop($array);
            unset($array[$key]);
            $array[$key] = $value;
            return $ret;
        } else {
            assert($entry_out !== null);
            // The key corresponds already to the first entry
            $array[$key] = $value;
            return $entry_out;
        }
    }

    /**
     * Sets an existant entry.
     * 
     * @param array &$array A reference to an array to update.
     * @param string|int $key The new key for the entry.
     * @param mixed $value The new value for the entry.
     * @return ?Entry
     *      The previous entry,
     *      or `null` if the entry is absent.
     * 
     * @template K of array-key
     * @template V
     * 
     * @phpstan-param array<K,V> &$array
     * @phpstan-param K $key
     * @phpstan-param V $value
     * @phpstan-return ($array is non-empty-array ? null|Entry<K,V> : null)
     */
    public static function setEntry(array &$array, string|int $key, mixed $value): ?Entry
    {
        if (empty($array))
            return null;

        $entry = Arrays::entry($array, $key);

        if (null === $entry)
            return null;

        $array[$key] = $value;
        return $entry;
    }

    // ========================================================================

    /**
     * Updates some existant entries in an array and add the unexistant ones.
     * 
     * @template K
     * @template V
     * 
     * @param array<K,V> &$array A reference to an array to update.
     * @param iterable<K,V> $update The (`$k => $v`) entries to set in the array.
     */
    public static function update(
        array &$array,
        iterable $update
    ): void {
        self::updateWithClosures($array, $update, self::updateEntry(...), self::updateEntry(...));
    }

    /**
     * Updates some existant entries in an array and returns the remaining unassigned entries of the updating.
     * 
     * @template K
     * @template V
     * 
     * @param array<K,V> &$array A reference to an array to update.
     * @param iterable<K,V> $update The (`$k => $v`) entries to update in the array.
     * @return array<K,V> The (`$k => $v`) entries of `$update` where `$k` is not a key of `$array`.
     */
    public static function updateIfPresent(
        array &$array,
        iterable $update,
    ): array {
        $remains = [];
        $fstore = function ($k, $v) use (&$remains): void {
            $remains[$k] = $v;
        };
        self::updateWithClosures($array, $update, self::updateEntry(...), $fstore);
        return $remains;
    }

    /**
     * Add the unexistant entries in an array and returns the remaining unassigned entries of the updating.
     * 
     * @template U
     * 
     * @param mixed[] &$array A reference to an array to update.
     * @param iterable<U> $update The (`$k => $v`) entries to add in the array.
     * @return U[] The (`$k => $v`) entries of `$update` where `$k` is a also a key of `$array` before the update.
     */
    public static function updateIfAbsent(
        array &$array,
        iterable $update,
    ): array {
        $remains = [];
        $fstore = function ($k, $v) use (&$remains): void {
            $remains[$k] = $v;
        };
        self::updateWithClosures($array, $update, $fstore, self::updateEntry(...));
        return $remains;
    }

    // ========================================================================
    // REMOVE

    /**
     * Deletes an entry from an array by its key and returns its value.
     * 
     * @template V
     * @template D
     * 
     * @param V[] &$array A reference to an array.
     * @param string|int $key The key of the entry to delete.
     * @param D $default A default value to be returned if the entry is not in the array.
     * @return V|D The removed entry value, if present, otherwise `$default`.
     * 
     * @deprecated Replaced by {@see Arrays::remove()}.
     */
    public static function removeEntry(array &$array, string|int $key, $default = null): mixed
    {
        if (!\array_key_exists($key, $array))
            return $default;

        $ret = $array[$key];
        unset($array[$key]);
        return $ret;
    }

    /**
     * Deletes an entry from an array by its key.
     * 
     * @param array &$array
     *      A reference to an array.
     * @param string|int $key
     *      The key of the entry to delete.
     * @return ?Entry
     *      The removed entry,
     *      or `null` if nothing was removed.
     * 
     * @template K
     * @template V
     * 
     * @phpstan-param array<K,V> $array
     * @phpstan-param array-key $key
     * @phpstan-return ($array is non-empty-array ? Entry<K,V> : null)
     */
    public static function remove(array &$array, string|int $key): ?Entry
    {
        $entry = Arrays::entry($array, $key);

        if (null === $entry)
            return null;

        unset($array[$key]);
        return $entry;
    }

    /**
     * Deletes some values from an array.
     * 
     * @param mixed[] &$array A reference to an array.
     * @param bool $strict If the comparison must be strict (`===`) or not (`==`).
     * @param mixed ...$vals Some values to delete.
     */
    public static function dropValues(array &$array, bool $strict, ...$vals): void
    {
        foreach ($vals as $val) {
            $k = \array_search($val, $array, $strict);

            if (false !== $k)
                unset($array[$k]);
        }
    }

    /**
     * Deletes some values from an array using the equality operator (`==`).
     * 
     * @param mixed[] &$array A reference to an array.
     * @param mixed ...$vals Some values to delete.
     */
    public static function dropEqualValues(array &$array, ...$vals): void
    {
        self::dropValues($array, false, ...$vals);
    }

    /**
     * Deletes some values from an array using the identity operator (`===`).
     * 
     * @param mixed[] &$array A reference to an array.
     * @param mixed ...$vals Some values to delete.
     */
    public static function dropSameValues(array &$array, ...$vals): void
    {
        self::dropValues($array, true, ...$vals);
    }

    /**
     * Removes some entries from an array according to a filter.
     * 
     * @template V
     * 
     * @param V[] $array An array.
     * @param Closure $filter A filter to apply on each entry of the array.
     *  If no callback is supplied, all empty entries of array will be removed.
     *  See `empty()` to know how PHP defines the empty semantic in this case.
     *  - `$filter(V $value):bool` (`$mode=0`)
     *  - `$filter(string|int $key):bool` (`$mode=ARRAY_FILTER_USE_KEY`)
     *  - `$filter(V $value, string|int $key):bool` (`$mode=ARRAY_FILTER_USE_BOTH`)
     * @param int $mode Flag determining what arguments are sent to callback:
     *  - `ARRAY_FILTER_USE_KEY` - pass key as the only argument to callback instead of the value
     *  - `ARRAY_FILTER_USE_BOTH` - pass both value and key as arguments to callback instead of the value
     *
     * Default is 0 which will pass value as the only argument to callback instead.
     * @return V[] An array of the removed entries.
     * 
     * @link https://www.php.net/manual/fr/function.empty.php empty()
     */
    public static function removeWithFilter(array &$array, ?Closure $filter = null, int $mode = 0): array
    {
        $drop = [];
        $ret = [];

        if ($filter === null) {
            $filter = fn($v) => empty($v);
            $mode = 0;
        }
        if ($mode === 0)
            $fmakeParams = fn($k, $v) => [$v];
        elseif ($mode === ARRAY_FILTER_USE_KEY)
            $fmakeParams = fn($k, $v) => [$k];
        elseif ($mode === ARRAY_FILTER_USE_BOTH)
            $fmakeParams = fn($k, $v) => [$v, $k];
        else
            throw new \Exception("Invalid mode $mode");

        foreach ($array as $k => $v) {
            $valid = $filter(...$fmakeParams($k, $v));

            if ($valid) {
                $drop[] = $k;
                $ret[$k] = $v;
            }
        }
        foreach ($drop as $d)
            unset($array[$d]);

        return $ret;
    }
}
