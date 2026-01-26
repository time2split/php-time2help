<?php

namespace Time2Split\Help\Tests\Memory;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Time2Split\Help\Tests\DataProvider\Provided;
use Time2Split\Help\Tests\Resource\AUnitEnum;
use Time2Split\Help\Memory\Memoizers;
use Time2Split\Help\Tests\Resource\BackedIntEnum;


final class MemoizerTest extends TestCase
{
    private const AllowedTypes = [
        [],
        [AUnitEnum::a],
        [AUnitEnum::a, AUnitEnum::b],
        [AUnitEnum::a, AUnitEnum::c],
        [AUnitEnum::a, AUnitEnum::b, AUnitEnum::c],
    ];

    private static function provideEnumCases(): array
    {
        $ret = [];

        foreach (self::AllowedTypes as $types) {
            $head = \implode(', ', \array_map(fn($t) => $t->name, $types));
            $ret[] = new Provided($head, [$types]);
        }
        return $ret;
    }

    public static function provideMemoize(): iterable
    {
        return Provided::merge(self::provideEnumCases());
    }

    #[DataProvider("provideMemoize")]
    public function testMemoize(array $cases): void
    {
        $memory = Memoizers::ofEnum(AUnitEnum::class);
        $set = $memory->memoize(...$cases);

        $this->assertSame($cases, \iterator_to_array($set->elements()));

        $setb = $memory->memoize(...$cases);
        $this->assertSame($set, $setb);
    }

    public function testMemoizeNotAllowed(): void
    {
        $memory = Memoizers::ofEnum(AUnitEnum::class, self::AllowedTypes);
        $this->expectException(\InvalidArgumentException::class);
        $memory->memoize(AUnitEnum::b);
    }

    public function testMemoizeInvalidEnumCase(): void
    {
        $memory = Memoizers::ofEnum(AUnitEnum::class);
        $this->expectException(\InvalidArgumentException::class);
        $memory->memoize(BackedIntEnum::a);
    }
}
