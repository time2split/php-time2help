<?php

namespace Time2Split\Help\Tests\Container\Memory;

use Time2Split\Help\Container\Entry;
use Time2Split\Help\Container\Set;
use Time2Split\Help\Container\Sets;
use Time2Split\Help\Functions;
use Time2Split\Help\Memory\EnumSetMemoizer;
use Time2Split\Help\Memory\Memoizers;
use Time2Split\Help\Tests\Container\AbstractContainerTestClass;
use Time2Split\Help\Tests\Resource\AUnitEnum;

class MemoizerContainerTest extends AbstractContainerTestClass
{
    #[\Override]
    protected static function provideContainer(): EnumSetMemoizer
    {
        return Memoizers::ofEnum(AUnitEnum::class);
    }


    private static function provideEnumCases(): array
    {
        return [
            [AUnitEnum::a],
            [AUnitEnum::b],
            [AUnitEnum::c],
            [AUnitEnum::d],
            [AUnitEnum::e],
            [AUnitEnum::a, AUnitEnum::b],
            [AUnitEnum::a, AUnitEnum::c],
        ];
    }

    /**
     * @return array<AUnitEnum[], Set<AUnitEnum>>
     */
    #[\Override]
    protected static function provideEntries(): array
    {
        $ret = [];

        foreach (self::provideEnumCases() as $cases)
            $ret[] = new Entry($cases, Sets::ofEnum()->putFromList($cases));

        return $ret;
    }

    #[\Override]
    protected static function provideContainerWithSubEntries(int $offset = 0, ?int $length = null): EnumSetMemoizer
    {
        $subject = self::provideContainer();
        $entries = self::provideEntries();
        $entries = \array_slice($entries, $offset, $length);

        foreach (Entry::traverseEntries($entries) as $cases => $set)
            $subject->memoize(...$cases);

        return $subject;
    }

    #[\Override]
    static function entriesEqualClosure_traversableTest(bool $strict = false): Closure
    {
        return fn(Entry $a, Entry $b) => self::entriesEquals($a, $b, $strict);
    }

    private static function entriesEquals(Entry $a, Entry $b, bool $strict)
    {
        $equals = Functions::getCallbackForEquals($strict);

        if (!$equals($a->key, $b->key))
            return false;

        /** @var Set<AUnitEnum> */
        $seta = $a->value;
        /** @var Set<AUnitEnum> */
        $setb = $b->value;

        if (!$equals($seta->toListOfElements(), $setb->toListOfElements()))
            return false;

        return true;
    }
}
