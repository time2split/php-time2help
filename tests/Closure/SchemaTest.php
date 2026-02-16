<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Closure;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use Time2Split\Help\Closure\Schema\ClosureSchema;
use Time2Split\Help\Closure\Schema\Impl\AbstractSchemaOfSchema;
use Time2Split\Help\Closure\Schema\ParameterSchema;
use Time2Split\Help\Closure\Schema\Schemas;

class SchemaTest extends TestCase
{
    public static function provideClosureShema(): array
    {
        $noReturn = fn() => 1;

        return [
            'has return' =>
            [\count(...), fn() => Schemas::closure()->hasReturnType()],
            'has return F !V' =>
            [\count(...), fn() => Schemas::closure()->hasReturnType(false), 'validate' => false],
            'has return F' =>
            [$noReturn, fn() => Schemas::closure()->hasReturnType(false)],
            'has return !V' =>
            [$noReturn, fn() => Schemas::closure()->hasReturnType(), 'validate' => false],


            'name is' =>
            [\count(...), fn() => Schemas::closure()->name()->is('count')],
            'short name is' =>
            [\count(...), fn() => Schemas::closure()->shortName()->is('count')],
            'namepace is' =>
            [\count(...), fn() => Schemas::closure()->namespace()->is('')],
            'returnType is' =>
            [\count(...), fn() => Schemas::closure()->returnType()->strIs('int')],
            'number of parameters is' =>
            [\count(...), fn() => Schemas::closure()->numberOfParameters()->is(2)],
            'number of required parameters is' =>
            [\count(...), fn() => Schemas::closure()->numberOfRequiredParameters()->is(1)],
            'param[0] is' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(0)->name()->is('value')->commit()],
            'param[1] is' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(1)->name()->is('mode')->commit()],

            'name !V' =>
            [\count(...), fn() => Schemas::closure()->name()->is('x'), 'validate' => false],
            'short name !V' =>
            [\count(...), fn() => Schemas::closure()->shortName()->is('x'), 'validate' => false],
            'namespace !V' =>
            [\count(...), fn() => Schemas::closure()->namespace()->is('x'), 'validate' => false],
            'returnType !V' =>
            [\count(...), fn() => Schemas::closure()->returnType()->strIs('x'), 'validate' => false],
            'number of parameters !V' =>
            [\count(...), fn() => Schemas::closure()->numberOfParameters()->is('x'), 'validate' => false],
            'number of required parameters is' =>
            [\count(...), fn() => Schemas::closure()->numberOfRequiredParameters()->is('x'), 'validate' => false],
            'param[0] !V' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(0)->name()->is('x')->commit(), 'validate' => false],
            'param[1] !V' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(1)->name()->is('x')->commit(), 'validate' => false],
        ];
    }

    #[DataProvider("provideClosureShema")]
    public function testClosureSchema(Closure $closure, Closure $getSchema, bool $validate = true): void
    {
        $schema = $getSchema();
        $this->assertInstanceOf(ClosureSchema::class, $schema);
        $this->assertSame($validate, $schema->validate($closure));
    }

    // =========================================================================

    public static function provideParameterBuilder(): array
    {
        $fn = fn(mixed $param, &$ref, $opt = 1, $const = PHP_INT_BITS, bool ...$vars) => 0;

        $param = new ReflectionParameter($fn, 0);
        $refer = new ReflectionParameter($fn, 1);
        $optio = new ReflectionParameter($fn, 2);
        $const = new ReflectionParameter($fn, 3);
        $varia = new ReflectionParameter($fn, 4);
        $promo = new ReflectionParameter([AbstractSchemaOfSchema::class, '__construct'], 0);

        return [
            'allows null' =>
            [$param, fn() => Schemas::parameter()->allowsNull()],
            'has type' =>
            [$param, fn() => Schemas::parameter()->hasType()],
            'is optional' =>
            [$optio, fn() => Schemas::parameter()->isOptional()],
            'is variadic' =>
            [$varia, fn() => Schemas::parameter()->isVariadic()],
            'is promoted' =>
            [$promo, fn() => Schemas::parameter()->isPromoted()],
            'is passed by ref' =>
            [$refer, fn() => Schemas::parameter()->isPassedByReference()],
            'can be passed by val' =>
            [$param, fn() => Schemas::parameter()->canBePassedByValue()],
            'is default val available' =>
            [$optio, fn() => Schemas::parameter()->isDefaultValueAvailable()],
            'is default val const' =>
            [$const, fn() => Schemas::parameter()->isDefaultValueConstant()],

            'allows null F' =>
            [$varia, fn() => Schemas::parameter()->allowsNull(false)],
            'has type F' =>
            [$refer, fn() => Schemas::parameter()->hasType(false)],
            'is optional F' =>
            [$param, fn() => Schemas::parameter()->isOptional(false)],
            'is variadic F' =>
            [$param, fn() => Schemas::parameter()->isVariadic(false)],
            'is promoted F' =>
            [$param, fn() => Schemas::parameter()->isPromoted(false)],
            'is passed by ref F' =>
            [$param, fn() => Schemas::parameter()->isPassedByReference(false)],
            'can be passed by val F' =>
            [$refer, fn() => Schemas::parameter()->canBePassedByValue(false)],
            'is default val available F' =>
            [$param, fn() => Schemas::parameter()->isDefaultValueAvailable(false)],
            'is default val const F' =>
            [$optio, fn() => Schemas::parameter()->isDefaultValueConstant(false)],

            'allows null!V' =>
            [$varia, fn() => Schemas::parameter()->allowsNull(), 'validate' => false],
            'has type!V' =>
            [$refer, fn() => Schemas::parameter()->hasType(), 'validate' => false],
            'is optional!V' =>
            [$param, fn() => Schemas::parameter()->isOptional(), 'validate' => false],
            'is variadic!V' =>
            [$param, fn() => Schemas::parameter()->isVariadic(), 'validate' => false],
            'is promoted!V' =>
            [$param, fn() => Schemas::parameter()->isPromoted(), 'validate' => false],
            'is passed by ref!V' =>
            [$param, fn() => Schemas::parameter()->isPassedByReference(), 'validate' => false],
            'can be passed by val!V' =>
            [$refer, fn() => Schemas::parameter()->canBePassedByValue(), 'validate' => false],
            'is default val available!V' =>
            [$param, fn() => Schemas::parameter()->isDefaultValueAvailable(), 'validate' => false],
            'is default val const!V' =>
            [$optio, fn() => Schemas::parameter()->isDefaultValueConstant(), 'validate' => false],

            'allows null F!V' =>
            [$param, fn() => Schemas::parameter()->allowsNull(false), 'validate' => false],
            'has type F!V' =>
            [$param, fn() => Schemas::parameter()->hasType(false), 'validate' => false],
            'is optional F!V' =>
            [$optio, fn() => Schemas::parameter()->isOptional(false), 'validate' => false],
            'is variadic F!V' =>
            [$varia, fn() => Schemas::parameter()->isVariadic(false), 'validate' => false],
            'is promoted F!V' =>
            [$promo, fn() => Schemas::parameter()->isPromoted(false), 'validate' => false],
            'is passed by ref F!V' =>
            [$refer, fn() => Schemas::parameter()->isPassedByReference(false), 'validate' => false],
            'can be passed by val F!V' =>
            [$param, fn() => Schemas::parameter()->canBePassedByValue(false), 'validate' => false],
            'is default val available F!V' =>
            [$optio, fn() => Schemas::parameter()->isDefaultValueAvailable(false), 'validate' => false],
            'is default val const F!V' =>
            [$const, fn() => Schemas::parameter()->isDefaultValueConstant(false), 'validate' => false],

            'name' =>
            [$param, fn() => Schemas::parameter()->name()->is('param')],
            'type' =>
            [$param, fn() => Schemas::parameter()->type()->name()->is('mixed')->commit()],
        ];
    }

    #[DataProvider("provideParameterBuilder")]
    public function testParameterBuilder(ReflectionParameter $param, Closure $getSchema, bool $validate = true): void
    {
        $schema = $getSchema();
        $this->assertInstanceOf(ParameterSchema::class, $schema);
        $this->assertSame($validate, $schema->validate($param));
    }

    // =========================================================================
}
