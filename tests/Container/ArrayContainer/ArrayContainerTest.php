<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\ArrayContainer;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\ArrayContainers;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Exception\UnmodifiableException;
use Time2Split\Help\Functions;
use Time2Split\Help\Tests\Container\AbstractArrayAccessContainerTestClass;

/**
 * @author Olivier Rodriguez (zuri)
 */
class ArrayContainerTest extends AbstractArrayAccessContainerTestClass
{
    #[\Override]
    protected static function entriesEqualClosure_putMethodTest(bool $strict = false): Closure
    {
        $eq = Functions::getCallbackForEquals($strict);
        return fn(
            Entry $expect,
            Entry $subject
        ) =>  $eq($expect->value, $subject->value);
    }

    #[\Override]
    protected static function arrayValueIsAbsent(mixed $value): bool
    {
        return null === $value;
    }

    #[\Override]
    protected static function arrayValueIsPresent(mixed $value): bool
    {
        return null !== $value;
    }

    #[\Override]
    protected static function provideContainer(): ArrayContainer
    {
        return ArrayContainers::create();
    }

    public final function testUnmodifiableArrayContainer(): void
    {
        $subject = static::provideContainer();
        $unmodifiable = $subject->unmodifiable();

        $this->expectException(UnmodifiableException::class);
        $unmodifiable->array_reverse();
    }


    public static function provideArrayCall(): array
    {
        return [
            'combine' => ['array_combine', [\range(6, 1)]],
            'diff' => ['array_diff', [\range('c', 'g')]],
            'filter' => ['array_filter', [fn($v) => $v > 'b']],
            'flip' => ['array_flip', []],
            'intersect' => ['array_intersect', [\range('c', 'g')]],
            'map' => ['array_map', [fn($v) => "$v$v"], [
                'A' => 'aa',
                'B' => 'bb',
                'C' => 'cc',
                'D' => 'dd',
                'E' => 'ee',
                'F' => 'ff',
            ]],
            'reverse' => ['array_reverse', []],
            'merge' => ['array_merge', [\range(1, 6)]],
            'replace' => ['array_merge', [['E' => 'x']]],
            'values' => ['array_values', []],
        ];
    }

    #[DataProvider('provideArrayCall')]
    public final function testArrayCall(string $call, array $args, ?array $expect = null): void
    {
        $data = array_combine(\range('A', 'F'), \range('a', 'f'));
        $subject = static::provideContainer()->updateEntries($data);

        if (!isset($expect))
            $expect = $call($data, ...$args);

        [$subject, $call](...$args);
        $this->assertSame($expect, $subject->toArray());
    }
}
