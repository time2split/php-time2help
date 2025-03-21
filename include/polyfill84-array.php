<?php

/**
 * @author Olivier Rodriguez (zuri)
 */

if (!function_exists('array_all')) {
    function array_all(array $array, callable $callback): bool
    {
        foreach ($array as $k => $v) {
            if (!$callback($v, $k))
                return false;
        }
        return true;
    }
}

if (!function_exists('array_any')) {
    function array_any(array $array, callable $callback): bool
    {
        foreach ($array as $k => $v) {
            if ($callback($v, $k))
                return true;
        }
        return false;
    }
}

if (!function_exists('array_find')) {
    function array_find(array $array, callable $callback): mixed
    {
        foreach ($array as $k => $v) {
            if ($callback($v, $k))
                return $v;
        }
        return null;
    }
}

if (!function_exists('array_find_key')) {
    function array_find_key(array $array, callable $callback): mixed
    {
        foreach ($array as $k => $v) {
            if ($callback($v, $k))
                return $k;
        }
        return null;
    }
}
