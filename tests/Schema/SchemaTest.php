<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Closure;

use ArrayObject;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionFunction;
use ReflectionParameter;
use ReflectionType;
use Time2Split\Help\Schema\IntSchema;
use Time2Split\Help\Schema\ObjectSchema;
use Time2Split\Help\Schema\OfSchemas;
use Time2Split\Help\Schema\Reflection\ClassSchema;
use Time2Split\Help\Schema\Reflection\ClosureSchema;
use Time2Split\Help\Schema\Reflection\ParameterSchema;
use Time2Split\Help\Schema\Reflection\TypeSchema;
use Time2Split\Help\Schema\Schema;
use Time2Split\Help\Schema\Schemas;
use Time2Split\Help\Schema\StringSchema;

class SchemaTest extends TestCase
{

    // =========================================================================

    public static function provideSchema(): array
    {
        $int = 10;
        $str = 'value is a string';

        return [
            [$int, fn() => Schemas::schema()->sameAs($int)],
            [$str, fn() => Schemas::schema()->sameAs($str)],
            [$int, fn() => Schemas::schema()->sameAs($int, $str)],
            [$int, fn() => Schemas::schema()->sameAs($str, $int)],
            [$str, fn() => Schemas::schema()->sameAs($int, $str)],
            [$str, fn() => Schemas::schema()->sameAs($str, $int)],

            [00, fn() => Schemas::schema()->sameAs(0, 10, 20)],
            [10, fn() => Schemas::schema()->sameAs(0, 10, 20)],
            [20, fn() => Schemas::schema()->sameAs(0, 10, 20)],

            [$int, fn() => Schemas::schema()->int()->is($int)],
            [$str, fn() => Schemas::schema()->string()->is($str)],
        ];
    }

    #[DataProvider("provideSchema")]
    public function testSchema(mixed $value, Closure $getSchema, bool $validate = true): void
    {
        $schema = $getSchema();
        $this->assertInstanceOf(Schema::class, $schema);
        $this->assertInstanceOf(OfSchemas::class, $schema);
        $this->assertSame($validate, $schema->validate($value));
    }

    // =========================================================================

    public static function provideClassSchema(): array
    {
        $class = ArrayObject::class;
        $object = new ArrayObject();
        $rclass = new ReflectionClass($class);

        return [
            [$class, fn() => Schemas::class()->name()->is($class)],
            [$object, fn() => Schemas::class()->name()->is($class)],
            [$rclass, fn() => Schemas::class()->name()->is($class)],

            [$class, fn() => Schemas::class()->shortName()->is('ArrayObject')],
            [$class, fn() => Schemas::class()->namespace()->is($rclass->getNamespaceName())],
        ];
    }

    #[DataProvider("provideClassSchema")]
    public function testClassSchema(mixed $class, Closure $getSchema, bool $validate = true): void
    {
        $schema = $getSchema();
        $this->assertInstanceOf(ClassSchema::class, $schema);
        $this->assertSame($validate, $schema->validate($class));
    }

    // =========================================================================

    public static function provideObjectSchema(): array
    {
        $aobject = new ArrayObject();
        $cpy = new ArrayObject();

        return [
            [$aobject, fn() => Schemas::object()->instanceOf(ArrayObject::class)],
            [$cpy, fn() => Schemas::object()->instanceOf(ArrayObject::class)],
            [clone $aobject, fn() => Schemas::object()->instanceOf(ArrayObject::class)],

            [$aobject, fn() => Schemas::object()->is($aobject)],
            [$aobject, fn() => Schemas::object()->is($cpy), false],
            [$aobject, fn() => Schemas::object()->is(clone $aobject), false],
        ];
    }

    #[DataProvider("provideObjectSchema")]
    public function testObjectSchema(mixed $value, Closure $getSchema, bool $validate = true): void
    {
        $schema = $getSchema();
        $this->assertInstanceOf(ObjectSchema::class, $schema);
        $this->assertSame($validate, $schema->validate($value));
    }

    // =========================================================================

    public static function provideClosureShema(): array
    {
        $noReturn = fn() => 1;
        $fn = fn(mixed $param, &$ref, $opt = 1, $const = PHP_INT_BITS, bool ...$vars) => 0;

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
            [\count(...), fn() => Schemas::closure()->returnType()->name()->is('int')->up()],
            'number of parameters is' =>
            [\count(...), fn() => Schemas::closure()->numberOfParameters()->is(2)],
            'number of required parameters is' =>
            [\count(...), fn() => Schemas::closure()->numberOfRequiredParameters()->is(1)],
            'param[0] is' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(0)->name()->is('value')->up()],
            'param[1] is' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(1)->name()->is('mode')->up()],

            'name !V' =>
            [\count(...), fn() => Schemas::closure()->name()->is('x'), 'validate' => false],
            'short name !V' =>
            [\count(...), fn() => Schemas::closure()->shortName()->is('x'), 'validate' => false],
            'namespace !V' =>
            [\count(...), fn() => Schemas::closure()->namespace()->is('x'), 'validate' => false],
            'returnType !V' =>
            [\count(...), fn() => Schemas::closure()->returnType()->name()->is('x')->up(), 'validate' => false],
            'number of parameters !V' =>
            [\count(...), fn() => Schemas::closure()->numberOfParameters()->is(0), 'validate' => false],
            'number of required parameters is' =>
            [\count(...), fn() => Schemas::closure()->numberOfRequiredParameters()->is(0), 'validate' => false],
            'param[0] !V' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(0)->name()->is('x')->up(), 'validate' => false],
            'param[1] !V' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(1)->name()->is('x')->up(), 'validate' => false],

            '_500' =>
            [$fn, fn() => Schemas::closure()->parameters(
                Schemas::parameter()->type()->name()->is('mixed')->up(),
                Schemas::parameter()->name()->is('ref')->isPassedByReference(),
            )],
            '_500F' =>
            [$fn, fn() => Schemas::closure()->parameters(
                Schemas::parameter()->type()->name()->is('mixed')->up(),
                Schemas::parameter()->name()->is('ref')->canBePassedByValue(),
            ), false],
            '_501F' =>
            [strlen(...), fn() => Schemas::closure()->parameters(
                Schemas::parameter(),
                Schemas::parameter(),
            ), false],
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

    public static function provideParameterSchema(): array
    {
        $fn = fn(mixed $param, &$ref, $opt = 1, $const = PHP_INT_BITS, bool ...$vars) => 0;

        $param = new ReflectionParameter($fn, 0);
        $refer = new ReflectionParameter($fn, 1);
        $optio = new ReflectionParameter($fn, 2);
        $const = new ReflectionParameter($fn, 3);
        $varia = new ReflectionParameter($fn, 4);
        $promo = new ReflectionParameter([new class() {
            public function __construct(private $promoted = null) {}
        }, '__construct'], 0);

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
            [$param, fn() => Schemas::parameter()->type()->name()->is('mixed')->up()],
        ];
    }

    #[DataProvider("provideParameterSchema")]
    public function testParameterSchema(ReflectionParameter $param, Closure $getSchema, bool $validate = true): void
    {
        $schema = $getSchema();
        $this->assertInstanceOf(ParameterSchema::class, $schema);
        $this->assertSame($validate, $schema->validate($param));
    }


    // =========================================================================

    public static function provideTypeSchema(): array
    {
        $fn = fn(
            int $int,
            int|float $union,
            Schema&OfSchemas $schemas,
            $noType,
            mixed $mixed,
            null $null,
        ) => null;

        $int = (new ReflectionParameter($fn, 0))->getType();
        $union = (new ReflectionParameter($fn, 1))->getType();
        $schema = (new ReflectionParameter($fn, 2))->getType();
        $noType = (new ReflectionParameter($fn, 3))->getType();
        $mixed = (new ReflectionParameter($fn, 4))->getType();
        $null = (new ReflectionParameter($fn, 4))->getType();

        return [
            'allows null' =>
            [$null, fn() => Schemas::type()->allowsNull()],
            'allows null F' =>
            [$int, fn() => Schemas::type()->allowsNull(false)],
            'allows null !V' =>
            [$int, fn() => Schemas::type()->allowsNull(), 'validate' => false],
            'allows null F!V' =>
            [$null, fn() => Schemas::type()->allowsNull(false), 'validate' => false],

            'name is' =>
            [$int, fn() => Schemas::type()->name()->is('int')],
            'name is !v' =>
            [$int, fn() => Schemas::type()->name()->is('x'), false],
        ];
    }

    #[DataProvider("provideTypeSchema")]
    public function testTypeSchema(ReflectionType $param, Closure $getSchema, bool $validate = true): void
    {
        $schema = $getSchema();
        $this->assertInstanceOf(TypeSchema::class, $schema);
        $this->assertSame($validate, $schema->validate($param));
    }


    // =========================================================================

    public static function provideStringSchema(): array
    {
        $a = "Try it now!";

        return [
            [$a, fn() => Schemas::string()->is($a)],
            [$a, fn() => Schemas::string()->startsWith('Try')],
            [$a, fn() => Schemas::string()->endsWith('now!')],
            [$a, fn() => Schemas::string()->contains('it')],
            [$a, fn() => Schemas::string()->pregMatch('/^T.*it.*!$/')],

            [$a, fn() => Schemas::string()->is("x$a"), false],
            [$a, fn() => Schemas::string()->startsWith('Ts'), false],
            [$a, fn() => Schemas::string()->endsWith('x!'), false],
            [$a, fn() => Schemas::string()->contains('iu'), false],
            [$a, fn() => Schemas::string()->pregMatch('/^Tt.*it.*!$/'), false],

            [$a, fn() => Schemas::string()->strlen()->is(11)],
        ];
    }

    #[DataProvider("provideStringSchema")]
    public function testStringSchema(string $string, Closure $getSchema, bool $validate = true): void
    {
        $schema = $getSchema();
        $this->assertInstanceOf(StringSchema::class, $schema);
        $this->assertSame($validate, $schema->validate($string));
    }

    // =========================================================================

    public static function provideIntSchema(): array
    {
        $i = 10;

        return [
            [$i, fn() => Schemas::int()->is($i)],
            [$i, fn() => Schemas::int()->between($i, $i)],
            [$i, fn() => Schemas::int()->between($i - 1, $i + 1)],
            [$i, fn() => Schemas::int()->between($i + 1, $i + 100), false],
            [$i, fn() => Schemas::int()->between($i - 100, $i - 1), false],


            [$i, fn() => Schemas::int()->max($i)],
            [$i, fn() => Schemas::int()->max($i + 1)],
            [$i, fn() => Schemas::int()->max($i - 1), false],

            [$i, fn() => Schemas::int()->min($i)],
            [$i, fn() => Schemas::int()->min($i - 1)],
            [$i, fn() => Schemas::int()->min($i + 1), false],

            '_100' =>
            [0, fn() => Schemas::int()->isPositive(), 'validate' => false],
            '_101' =>
            [0, fn() => Schemas::int()->isPositive(yes: false)],
            '_102' =>
            [0, fn() => Schemas::int()->isPositive(false)],
            '_103' =>
            [0, fn() => Schemas::int()->isPositive(false, yes: false), 'validate' =>  false],

            '_105' =>
            [1, fn() => Schemas::int()->isPositive()],
            '_106' =>
            [1, fn() => Schemas::int()->isPositive(yes: false), 'validate' =>  false],
            '_107' =>
            [1, fn() => Schemas::int()->isPositive(false)],
            '_108' =>
            [1, fn() => Schemas::int()->isPositive(false, yes: false), 'validate' =>  false],

            '_110' =>
            [-1, fn() => Schemas::int()->isPositive(), 'validate' => false],
            '_111' =>
            [-1, fn() => Schemas::int()->isPositive(yes: false)],
            '_112' =>
            [-1, fn() => Schemas::int()->isPositive(false), 'validate' =>  false],
            '_113' =>
            [-1, fn() => Schemas::int()->isPositive(false, yes: false)],

            '_200' =>
            [0, fn() => Schemas::int()->isNegative(), 'validate' => false],
            '_201' =>
            [0, fn() => Schemas::int()->isNegative(yes: false)],
            '_202' =>
            [0, fn() => Schemas::int()->isNegative(false)],
            '_203' =>
            [0, fn() => Schemas::int()->isNegative(false, yes: false), 'validate' =>  false],

            '_205' =>
            [1, fn() => Schemas::int()->isNegative(), 'validate' => false],
            '_206' =>
            [1, fn() => Schemas::int()->isNegative(yes: false)],
            '_207' =>
            [1, fn() => Schemas::int()->isNegative(false), 'validate' => false],
            '_208' =>
            [1, fn() => Schemas::int()->isNegative(false, yes: false)],

            '_210' =>
            [-1, fn() => Schemas::int()->isNegative()],
            '_211' =>
            [-1, fn() => Schemas::int()->isNegative(yes: false), 'validate' => false],
            '_212' =>
            [-1, fn() => Schemas::int()->isNegative(false)],
            '_213' =>
            [-1, fn() => Schemas::int()->isNegative(false, yes: false), 'validate' =>  false],

        ];
    }

    #[DataProvider("provideIntSchema")]
    public function testIntSchema(int $int, Closure $getSchema, bool $validate = true): void
    {
        $schema = $getSchema();
        $this->assertInstanceOf(IntSchema::class, $schema);
        $this->assertSame($validate, $schema->validate($int));
    }

    // ========================================================================

    public static function provideComplexSchema(): array
    {
        $fn = fn(int $int): bool|string => true;

        return [
            [0, fn() => Schemas::int()->and()->is(0)],
            [0, fn() => Schemas::int()->and()->and()->is(0)],
            [0, fn() => Schemas::int()->up()->is(0)],
            [0, fn() => Schemas::int()->up()->and()->is(0)],
            [0, fn() => Schemas::int()->and()->up()->is(0)],

            '_10' =>
            [0, fn() => Schemas::int()->schema(Schemas::string(true)->is('0'))],
            '_11' =>
            [0, fn() => Schemas::int()->schema(Schemas::string(false)->is('0')), false],

            '_100' =>
            [
                10,
                fn() => Schemas::schema()
                    ->int()->is(10)
                    ->string(true)->startsWith('1'),
            ],
            '_101' =>
            [
                10,
                fn() => Schemas::schema()
                    ->int()->is(10)
                    ->string(true)->startsWith('0'),
                false
            ],
            '_102' =>
            [
                10,
                fn() => Schemas::schema()
                    ->string(true)->startsWith('1')
                    ->int()->is(10),
            ],
            '_103' =>
            [
                10,
                fn() => Schemas::schema()
                    ->string(true)->startsWith('0')
                    ->int()->is(10),
                false
            ],


            '_500' =>
            [
                $fn,
                fn() => Schemas::closure()->schema(Schemas::fromClosure(
                    function (ReflectionFunction $function) {
                        return $function->getShortName() === '{closure}';
                    }
                ))
            ],
            '_501' =>
            [
                $fn,
                fn() => Schemas::closure()->schema(Schemas::fromClosure(
                    function (ReflectionFunction $function) {
                        return $function->getShortName() === '{closure}';
                    }
                ))->numberOfParameters()->isPositive()
            ],
            '_501F' =>
            [
                $fn,
                fn() => Schemas::closure()->schema(Schemas::fromClosure(
                    function (ReflectionFunction $function) {
                        return $function->getShortName() === '{closure}';
                    }
                ))->numberOfParameters()->isNegative(),
                false
            ],
            '_502' =>
            [
                $fn,
                fn() => Schemas::closure()
                    ->numberOfParameters()->isPositive()
                    ->schema(Schemas::fromClosure(
                        function (ReflectionFunction $function) {
                            return $function->getShortName() === '{closure}';
                        }
                    ))
            ],
            '_502F' =>
            [
                $fn,
                fn() => Schemas::closure()
                    ->numberOfParameters()->isNegative()
                    ->schema(Schemas::fromClosure(
                        function (ReflectionFunction $function) {
                            return $function->getShortName() === '{closure}';
                        }
                    )),
                false
            ],

            '_510' =>
            [
                $fn,
                fn() => Schemas::closure()
                    ->schema(Schemas::closure()->shortName()->is('{closure}')),
            ],
            '_510F' =>
            [
                $fn,
                fn() => Schemas::closure()
                    ->schema(Schemas::closure()->shortName()->is('')),
                false
            ],
        ];
    }

    #[DataProvider("provideComplexSchema")]
    public function testComplexSchema(mixed $element, Closure $getSchema, bool $validate = true): void
    {
        $schema = $getSchema();
        $this->assertSame($validate, $schema->validate($element));
    }

    // ========================================================================

    public function testOpSchemaReturnsThis(): void
    {
        $a = Schemas::closure();
        $b = $a->schema(Schemas::int());
        $this->assertSame($a, $b);

        $a = Schemas::closure();
        $s = $a->string()->up();
        $b = $s->schema(Schemas::int());
        $this->assertSame($a, $b);
    }

    public function testOpSchemaUnmodifiableChild(): void
    {
        $roota = Schemas::int();
        $this->assertTrue($roota->validate(100));

        // The tyoe of b must be different from a to avoid merging the child list
        $rootb = Schemas::schema()->schema($roota);
        $this->assertTrue($rootb->validate(100));

        $roota->int()->is(10);
        $this->assertFalse($roota->validate(100));
        $this->assertTrue($rootb->validate(100));
    }

    public function testOpCommitDoClone(): void
    {
        $roota = Schemas::int()->min(100);
        $this->assertTrue($roota->validate(100));

        $rootb = $roota;
        $rootb->min(200);
        $this->assertFalse($roota->validate(100));
        $this->assertFalse($rootb->validate(100));
        $this->assertTrue($roota->validate(200));
        $this->assertTrue($rootb->validate(200));

        $ca = $roota->commit();
        $cb = $rootb->commit();
        $rootb->min(300);
        $this->assertFalse($rootb->validate(200));
        $this->assertTrue($rootb->validate(300));
        $this->assertTrue($ca->validate(200));
        $this->assertTrue($cb->validate(200));
    }

    public function testOpCommitUnmodifiableException(): void
    {
        $s = Schemas::int()->min(100)->commit();
        $this->expectException(\Error::class);
        // Undefined method
        $s->min(100);
    }
}
