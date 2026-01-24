<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests;

use Time2Split\Diff\Algorithm\Myers;
use Time2Split\Diff\DiffInstruction;
use Time2Split\Diff\DiffInstructionType;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Functions;
use Time2Split\Help\Iterables;

/**
 * Functions for making quick diff reports.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\tests
 */
final class DiffReports
{
    use NotInstanciable;

    private static function getDiffTypeChar(DiffInstructionType $type): string
    {
        return match ($type) {
            DiffInstructionType::Drop => ' - ',
            DiffInstructionType::Keep => '   ',
            DiffInstructionType::Insert => ' + ',
        };
    }

    public static function textReportOfList(iterable $editScript, ?callable $toString = null): string
    {
        \ob_start();
        /**
         * @var DiffInstruction $i
         */
        foreach ($editScript as $i) {
            $op = self::getDiffTypeChar($i->type);
            $item = Functions::basicToString($i->item, $toString);
            echo "$op$item\n";
        }
        return \ob_get_clean();
    }

    public static function textReport(iterable $a, iterable $b, ?callable $toString = null): string
    {
        $diff = Myers::diffList($a, $b);
        return self::textReportOfList($diff, $toString);
    }


    public static function listDiffTextReport(iterable $a, iterable $b, ?callable $toString = null): string
    {
        $absentFromB = Iterables::valuesInjectionDiff($a, $b);
        $absentFromA = Iterables::valuesInjectionDiff($b, $a);
        $inBoth = Iterables::findEntriesRelations(fn($k, $v, $b) => \in_array($v, $b), $a, $b);

        \ob_start();
        foreach ($absentFromA as $i) {
            $op = self::getDiffTypeChar(DiffInstructionType::Drop);
            $item = Functions::basicToString($i, $toString);
            echo "$op$item\n";
        }
        foreach ($inBoth as $i) {
            $op = self::getDiffTypeChar(DiffInstructionType::Keep);
            $item = Functions::basicToString($i, $toString);
            echo "$op$item\n";
        }
        foreach ($absentFromB as $i) {
            $op = self::getDiffTypeChar(DiffInstructionType::Insert);
            $item = Functions::basicToString($i, $toString);
            echo "$op$item\n";
        }
        return \ob_get_clean();
    }
}
