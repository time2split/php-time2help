<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Closure;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Time2Split\Help\Closure\Captures;
use Time2Split\Help\Closure\ParameterInjection;
use Time2Split\Help\Closure\ParameterInjections;

final class CaptureTest extends TestCase
{
    private static function fun(mixed $a, mixed $b, mixed $c, mixed $d, mixed $e): array
    {
        return [$a, $b, $c, $d];
    }

    public static function provideComplexInjection(): array
    {
        return [
            'b' => [
                ParameterInjections::injectValue(Captures::validParameterNameClosure('b'), 'B'),
                [1, 'B', 2, 3]
            ],
            'bd' => [
                [
                    ParameterInjections::injectValue(Captures::validParameterNameClosure('b'), 'B'),
                    ParameterInjections::injectValue(Captures::validParameterNameClosure('d'), 'D'),
                ],
                [1, 'B', 2, 'D']
            ],
            'ab' => [
                [
                    ParameterInjections::injectValue(Captures::validParameterNameClosure('a'), 'A'),
                    ParameterInjections::injectValue(Captures::validParameterNameClosure('b'), 'B'),
                ],
                ['A', 'B', 1, 2]
            ],
            'ba' => [
                [
                    ParameterInjections::injectValue(Captures::validParameterNameClosure('b'), 'B'),
                    ParameterInjections::injectValue(Captures::validParameterNameClosure('a'), 'A'),
                ],
                ['A', 'B', 1, 2]
            ],
            'ac' =>  [
                [
                    ParameterInjections::injectValue(Captures::validParameterNameClosure('a'), 'A'),
                    ParameterInjections::injectValue(Captures::validParameterNameClosure('c'), 'C'),
                ],
                ['A', 1, 'C', 2]
            ],
            'ad' =>   [
                [
                    ParameterInjections::injectValue(Captures::validParameterNameClosure('a'), 'A'),
                    ParameterInjections::injectValue(Captures::validParameterNameClosure('d'), 'D'),
                ],
                ['A', 1, 2, 'D']
            ],
        ];
    }

    #[DataProvider('provideComplexInjection')]
    public function testComplexInjection(array|ParameterInjection $injection, array $expect): void
    {
        $call = Captures::inject(self::fun(...), $injection);
        $this->assertTrue($call->isPresent(), 'is callable');
        $res = $call->get()(1, 2, 3, 4, 5);
        $this->assertSame($expect, $res);
    }
    // ========================================================================

    public static function provideSeatchInsertionPoint(): array
    {
        return [
            [
                \array_flip(...),
                ParameterInjections::injectValue(Captures::validParameterNameClosure('array')),
                0
            ],
            [
                \array_map(...),
                ParameterInjections::injectValue(Captures::validParameterTypeClosure('callable')),
                0
            ],
            [
                \array_map(...),
                ParameterInjections::injectValue(Captures::validParameterNameClosure('array')),
                1
            ],
            [
                \array_map(...),
                ParameterInjections::injectValue(Captures::validParameterPositionClosure(2)),
                2
            ],
            [
                \count(...),
                ParameterInjections::injectValue(Captures::validParameterNameClosure('array')),
                null
            ],
        ];
    }

    #[DataProvider('provideSeatchInsertionPoint')]
    public function testSearchInsertionPoint(
        Closure $function,
        ParameterInjection $injection,
        ?int $pos
    ): void {
        $point = Captures::captureParameters($function, [$injection]);

        if (isset($pos))
            $this->assertSame($pos, $point[0]->parameter->getPosition());
        else
            $this->assertEmpty($point);
    }

    // ========================================================================

    public function testRef()
    {
        $a = 0;
        $inj = ParameterInjections::injectReference(fn() => true, $a);
        $a = 5;
        $this->assertSame($a, $inj->value);
        $inj->value = 'xxx';
        $this->assertSame($a, $inj->value);
    }

    public static function provideCall(): array
    {
        $array = \range('a', 'f');
        $map = fn($x) => "x$x";

        return [
            [
                \array_flip(...),
                ParameterInjections::injectValue(
                    Captures::validParameterNameClosure('array'),
                    $array
                ),
                \array_flip($array)
            ],
            [
                \array_map(...),
                ParameterInjections::injectValue(
                    Captures::validParameterNameClosure('array'),
                    $array
                ),
                \array_map($map, $array),
                $map
            ],
        ];
    }

    #[DataProvider('provideCall')]
    public function testCall(
        Closure $function,
        ParameterInjection $injection,
        mixed $expect,
        mixed ...$args
    ): void {
        $lastArg = $injection->value;
        $call = Captures::inject($function, $injection);
        $this->assertTrue($call->isPresent(), 'point is present');
        $call = $call->get();

        $res = $call(...$args);
        $this->assertSame($expect, $res, 'result');
        $this->assertSame($lastArg, $injection->value, 'ref unmodified');
    }

    // ========================================================================

    public static function provideRefCall(): array
    {
        $a = $b = $array = \range('a', 'f');

        return [
            'array_flip' => [
                self::makeItRef(\array_flip(...)),
                ParameterInjections::injectReference(
                    Captures::validParameterNameClosure('first'),
                    $a
                ),
                \array_flip($array)
            ],
            'array_flip.return' => [
                self::makeItRef(\array_flip(...), true),
                ParameterInjections::injectReference(
                    Captures::validParameterNameClosure('first'),
                    $b
                ),
                \array_flip($array),
                true
            ],
        ];
    }

    private static function makeItRef(Closure $theFunction, bool $return = false)
    {
        return function (array &$first, mixed ...$arguments) use ($theFunction, $return): mixed {
            $f = $first;
            $first = $theFunction($f, ...$arguments);

            if ($return)
                return $first;

            return null;
        };
    }

    #[DataProvider('provideRefCall')]
    public function testRefCall(
        Closure $function,
        ParameterInjection $injection,
        mixed $expect,
        bool $return = false
    ): void {
        $lastArg = $injection->value;
        $call = Captures::inject($function, $injection);
        $this->assertTrue($call->isPresent(), 'is callable');
        $call = $call->get();

        $res = $call();

        if ($return)
            $this->assertSame($expect, $res, 'result');
        else
            $this->assertNull($res, 'result');

        $this->assertSame($expect, $injection->value, 'injection value');
        $this->assertNotSame($lastArg, $injection->value, 'base arg changes');
    }
}
