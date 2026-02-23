<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Time2Split\Help\Arrays;
use Time2Split\Help\Container\Entry;

final class ArraysMapTest extends TestCase
{

    // ========================================================================

    /**
     * @phpstan-return mixed[]
     */
    public static function provideFNMapKeySignature(): array
    {
        return [
            'i:i' =>
            [fn(int $k): int => 0],
            'i:s' =>
            [fn(int $k): string => ''],
            'i:is' =>
            /** @phpstan-ignore return.unusedType */
            [fn(int $k): int|string => ''],

            's:i' =>
            [fn(string $k): int => 0],
            's:s' =>
            [fn(string $k): string => ''],
            's:is' =>
            /** @phpstan-ignore return.unusedType */
            [fn(string $k): int|string => ''],

            'is:i' =>
            [fn(int|string $k): int => 0],
            'is:s' =>
            [fn(int|string $k): string => ''],
            'is:is' =>
            /** @phpstan-ignore return.unusedType */
            [fn(int|string $k): int|string => ''],

            '+:' =>
            [fn(int $k, int $more): int => 0, false],
            '[+]:' =>
            [fn(int $k, int $opt = 1): int => 0],
        ];
    }

    #[DataProvider("provideFNMapKeySignature")]
    public function testFNMapKeySignature(\Closure $closure, bool $validate = true): void
    {
        $this->assertSame($validate, Arrays::fnSignatureIsMapKey($closure));
    }

    // ========================================================================

    /**
     * @phpstan-return mixed[]
     */
    public static function provideFNMapValueSignature(): array
    {
        return [
            ':' =>
            [function ($v) {}],
            'i:' =>
            [function (int $v) {}],
            'i:i' =>
            [fn(int $v): int => 0],
            'i:?i' =>
            /** @phpstan-ignore return.unusedType */
            [fn(int $v): ?int => 0],

            '+:' =>
            [function ($v, $more) {}, false],
            '[+]' =>
            [function ($v, $more = 0) {}],

            ':void' =>
            [function ($value): void {}, false],
        ];
    }

    #[DataProvider("provideFNMapValueSignature")]
    public function testFNMapValueSignature(\Closure $closure, bool $validate = true): void
    {
        $this->assertSame($validate, Arrays::fnSignatureIsMapValue($closure));
    }

    // ========================================================================

    /**
     * @phpstan-return mixed[]
     */
    public static function provideFNMapEntrySignature(): array
    {
        $entry = new Entry(0, 0);
        return [
            ':e' =>
            [fn($k, $v): Entry => $entry],

            '+:e' =>
            [fn($k, $v, $more): Entry => $entry, false],
            '[+]:e' =>
            [fn($k, $v, $more = 0): Entry => $entry],

            ':' =>
            [function ($k, $v) {}, false],
            ':v' =>
            [function ($k, $v): void {}, false],
            ':i' =>
            [fn($k, $v): int => 0, false],
        ];
    }

    #[DataProvider("provideFNMapEntrySignature")]
    public function testFNMapEntrySignature(\Closure $closure, bool $validate = true): void
    {
        $this->assertSame($validate, Arrays::fnSignatureIsMapEntry($closure));
    }
}
