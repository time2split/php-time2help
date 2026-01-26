<?php

declare(strict_types=1);

namespace Time2Split\Diff\Algorithm;

use Time2Split\Diff\DiffInstruction;
use Time2Split\Diff\DiffInstructions;
use Time2Split\Diff\DiffInstructionType;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Functions;

/**
 * A Myers diff algorithm implementation.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\diff
 */
final class Myers
{
    use NotInstanciable;

    private static function traceToDiffInstructions(array $trace, array $a, array $b): iterable
    {
        $x = 0;
        $y = 0;

        foreach ($trace as [$tx, $ty]) {

            if ($tx - $ty > $x - $y) {
                do {
                    yield DiffInstructions::createInstruction(DiffInstructionType::Drop, $a[$x]);
                    $x++;
                } while ($tx - $ty > $x - $y);
            } else {

                while ($tx - $ty < $x - $y) {
                    yield DiffInstructions::createInstruction(DiffInstructionType::Insert, $b[$y]);
                    $y++;
                }
            }
            while ($x < $tx) {
                yield DiffInstructions::createInstruction(DiffInstructionType::Keep, $a[$x]);
                $x++;
                $y++;
            }
        }
    }

    private static function backtrack(array $vstack, int $x, int $y): array
    {
        $trace = [];
        $d = \max($x, $y);

        while (!empty($vstack)) {
            $v = \array_pop($vstack);
            $k = $x - $y;
            $trace[] = [$x, $y];

            if ($k === -$d || $k !== $d && $v[$k - 1] < $v[$k + 1])
                $k_prev = $k + 1;
            else
                $k_prev = $k - 1;

            $x = $v[$k_prev];
            $y = $x - $k_prev;
            $d--;
        }
        return \array_reverse($trace);
    }

    private static function _diff(array $a, array $b, ?callable $equals = null): array
    {
        $equals ??= Functions::equals(...);

        $n = \count($a);
        $m = \count($b);
        $max = $m + $n;

        $vstack = [];
        $v = \array_fill(-$m, $max + 1, 0);

        for ($d = 0; $d <= $max; $d++) {
            $vstack[] = $v;

            for ($k = -$d; $k <= $d; $k += 2) {

                if ($k === -$d || $k !== $d && $v[$k - 1] < $v[$k + 1])
                    $x = $v[$k + 1];
                else
                    $x = $v[$k - 1] + 1;

                $y = $x - $k;

                while ($x < $n && $y < $m && $equals($a[$x], $b[$y])) {
                    $x++;
                    $y++;
                }
                $v[$k] = $x;

                if ($x >= $n && $y >= $m)
                    break 2;
            }
        }
        return $vstack;
    }

    /**
     * Compare two lists of values using an equality closure.
     * 
     * @param iterable<*> $a
     *      The first list.
     * @param iterable<*> $b
     *      The second list.
     * @param null|\Closure(mixed $a,mixed $b):true $equals
     *      The equality closure.
     * 
     *      - $equals($a,$b): bool
     * 
     *      If set to null then the used closure is {@see Time2Split\Help\Functions::equals()}.
     * @return \Generator<int,DiffInstruction>
     *      The list of diff instructions.
     */
    public static function diff(iterable $a, iterable $b, ?\Closure $equals = null): \Generator
    {
        $a = \iterator_to_array($a);
        $b = \iterator_to_array($b);

        $vstack = self::_diff($a, $b, $equals);
        $trace = self::backtrack($vstack, \count($a), \count($b));
        return self::traceToDiffInstructions($trace, $a, $b);
    }

    /**
     * Compare two lists of values using an equality closure.
     * 
     * @param iterable<*> $a
     *      The first list.
     * @param iterable<*> $b
     *      The second list.
     * @param null|\Closure(mixed $a,mixed $b):true $equals
     *      The equality closure.
     * 
     *      - $equals($a,$b): bool
     * 
     *      If set to null then the used closure is {@see Time2Split\Help\Functions::equals()}.
     * @return array<int,DiffInstruction>
     *      The list of diff instructions.
     */
    public static function diffList(iterable $a, iterable $b, ?\Closure $equals = null): array
    {
        return \iterator_to_array(self::diff($a, $b, $equals));
    }
}
