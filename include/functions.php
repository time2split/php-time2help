<?php

/**
 * @author Olivier Rodriguez (zuri)
 */

use Time2Split\Help\Functions;
use Time2Split\Help\Iterables;

define('PHP_INT_BITS', (int)\log(PHP_INT_MAX, 2) + 1);

/**
 * Prints human-readable information about some values on STDERR.
 * 
 * @param mixed ...$values The values to print.
 */
function error_dump(mixed ...$values): void
{
    foreach ($values as $p)
        fwrite(STDERR, print_r($p, true) . "\n");
}

/**
 * Prints human-readable information about some values on STDERR,
 * then call exit().
 * 
 * @param mixed ...$values The values to print.
 */
function error_dump_exit(mixed ...$values): void
{
    error_dump(...$values);
    exit;
}

/**
 * Prints information about some values on STDERR using Functions::basicToString.
 * 
 * @param mixed ...$values The values to print.
 */
function error_dump_basic(mixed ...$item): void
{
    foreach ($item as $i)
        echo Functions::basicToString($i), "\n";
}

/**
 * Prints information about some values on STDERR using Functions::basicToString,
 * then call exit().
 * 
 * @param mixed ...$values The values to print.
 */
function error_dump_basic_exit(mixed ...$item): void
{
    foreach ($item as $i)
        echo Functions::basicToString($i), "\n";
    exit;
}

/**
 * Whether a value is a list
 * 
 * @param mixed $value A value to check.
 * 
 * @link https://www.php.net/manual/en/function.array-is-list.php array_is_list()
 */
function is_array_list(mixed $value): bool
{
    return \is_array($value) && \array_is_list($value);
}

/**
 * Whether all values are valid array keys (int|string).
 */
function is_list_of_array_keys(iterable $list)
{
    return Iterables::all($list, fn($v) => \is_int($v) || \is_string($v));
}
