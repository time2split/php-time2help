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
use Time2Split\Help\Iterables;
use Time2Split\Help\Schema\OfSchemas;
use Time2Split\Help\Schema\Operator\AndSchema;
use Time2Split\Help\Schema\Reflection\ClassSchema;
use Time2Split\Help\Schema\Reflection\ClosureSchema;
use Time2Split\Help\Schema\Reflection\ParameterSchema;
use Time2Split\Help\Schema\Reflection\TypeSchema;
use Time2Split\Help\Schema\Scalar\IntSchema;
use Time2Split\Help\Schema\Scalar\ObjectSchema;
use Time2Split\Help\Schema\Scalar\StringSchema;
use Time2Split\Help\Schema\Schema;
use Time2Split\Help\Schema\Schemas;
use Time2Split\Help\Tests\DataProvider\Provided;

class SchemaTest extends TestCase
{

    /**
     * @phpstan-param Provided[] $schemas
     * @phpstan-param mixed[] $values
     * @phpstan-return iterable<mixed[]>
     */
    private static function makeProvidedForSchemas(
        array $schemas,
        array $values,
        bool $validate = true,
    ): iterable {

        $vres = [];
        foreach ($values as $v) {
            $vres[] = new Provided("$v", [$v]);
        }
        $validate = new Provided($validate ? "T" : "F", [$validate]);
        return Provided::merge($vres, $schemas, [$validate]);
    }

    // =========================================================================

    /**
     * @phpstan-return array<mixed>
     */
    public static function provideSchema(): array
    {
        $int = 10;
        $str = '1 value is a string';

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

            [$int, fn() => Schemas::schema()->isOfType('integer')],
            [$str, fn() => Schemas::schema()->isOfType('string')],
            [$int, fn() => Schemas::schema()->isOfType('integer', 'string')],
            [$str, fn() => Schemas::schema()->isOfType('integer', 'string')],
            [$int, fn() => Schemas::schema()->isOfType('string', 'integer')],
            [$str, fn() => Schemas::schema()->isOfType('string', 'integer')],

            [$int, fn() => Schemas::schema()->string(), false],
            [$str, fn() => Schemas::schema()->integer(), false],
            [$str, fn() => Schemas::schema()->string()],
            [$int, fn() => Schemas::schema()->integer()],

            [$int, fn() => Schemas::schema()->toString()->is("$int")],
            [$str, fn() => Schemas::schema()->toInteger()->is(1)],
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

    /**
     * @phpstan-return array<mixed>
     */
    public static function provideClassSchema(): array
    {
        $class = ArrayObject::class;
        $object = new ArrayObject();
        $rclass = new ReflectionClass($class);

        return [
            [$class, fn() => Schemas::class()->name()->is($class)->up()],
            [$object, fn() => Schemas::class()->name()->is($class)->up()],
            [$rclass, fn() => Schemas::class()->name()->is($class)->up()],

            [$class, fn() => Schemas::class()->shortName()->is('ArrayObject')->up()],
            [$class, fn() => Schemas::class()->namespace()->is($rclass->getNamespaceName())->up()],
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

    /**
     * @phpstan-return array<mixed>
     */
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

    /**
     * @phpstan-return array<mixed>
     */
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
            [\count(...), fn() => Schemas::closure()->name()->is('count')->up()],
            'short name is' =>
            [\count(...), fn() => Schemas::closure()->shortName()->is('count')->up()],
            'namepace is' =>
            [\count(...), fn() => Schemas::closure()->namespace()->is('')->up()],
            'returnType is' =>
            [\count(...), fn() => Schemas::closure()->returnType()->name()->is('int')->up(2)],
            'number of parameters is' =>
            [\count(...), fn() => Schemas::closure()->numberOfParameters()->is(2)->up()],
            'number of required parameters is' =>
            [\count(...), fn() => Schemas::closure()->numberOfRequiredParameters()->is(1)->up()],
            'param[0] is' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(0)->name()->is('value')->up(2)],
            'param[1] is' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(1)->name()->is('mode')->up(2)],

            'name !V' =>
            [\count(...), fn() => Schemas::closure()->name()->is('x')->up(), 'validate' => false],
            'short name !V' =>
            [\count(...), fn() => Schemas::closure()->shortName()->is('x')->up(), 'validate' => false],
            'namespace !V' =>
            [\count(...), fn() => Schemas::closure()->namespace()->is('x')->up(), 'validate' => false],
            'returnType !V' =>
            [\count(...), fn() => Schemas::closure()->returnType()->name()->is('x')->up(2), 'validate' => false],
            'number of parameters !V' =>
            [\count(...), fn() => Schemas::closure()->numberOfParameters()->is(0)->up(), 'validate' => false],
            'number of required parameters is !V' =>
            [\count(...), fn() => Schemas::closure()->numberOfRequiredParameters()->is(0)->up(), 'validate' => false],
            'param[0] !V' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(0)->name()->is('x')->up(2), 'validate' => false],
            'param[1] !V' =>
            [\count(...), fn() => Schemas::closure()->parameterAt(1)->name()->is('x')->up(2), 'validate' => false],

            '_500' =>
            [$fn, fn() => Schemas::closure()->parameters(
                Schemas::parameter()->type()->name()->is('mixed')->up(2, returnsClass: ParameterSchema::class),
                Schemas::parameter()->name()->is('ref')->up(returnsClass: ParameterSchema::class)->isPassedByReference(),
            )],
            '_500F' =>
            [$fn, fn() => Schemas::closure()->parameters(
                Schemas::parameter()->type()->name()->is('mixed')->up(2, returnsClass: ParameterSchema::class),
                Schemas::parameter()->name()->is('ref')->up(returnsClass: ParameterSchema::class)->canBePassedByValue(),
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

    /**
     * @phpstan-return array<mixed>
     */
    public static function provideParameterSchema(): array
    {
        $fn = fn(mixed $param, &$ref, $opt = 1, $const = PHP_INT_BITS, bool ...$vars) => 0;

        $param = new ReflectionParameter($fn, 0);
        $refer = new ReflectionParameter($fn, 1);
        $optio = new ReflectionParameter($fn, 2);
        $const = new ReflectionParameter($fn, 3);
        $varia = new ReflectionParameter($fn, 4);
        $promo = new ReflectionParameter([new class() {
            /** @phpstan-ignore missingType.parameter */
            public function __construct(
                /** @phpstan-ignore property.onlyWritten */
                private $promoted = null
            ) {}
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
            [$param, fn() => Schemas::parameter()->name()->is('param')->up()],
            'type' =>
            [$param, fn() => Schemas::parameter()->type()->name()->is('mixed')->up(2)],
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

    /**
     * @phpstan-return array<mixed>
     */
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
            [$int, fn() => Schemas::type()->name()->is('int')->up()],
            'name is !v' =>
            [$int, fn() => Schemas::type()->name()->is('x')->up(), false],
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

    /**
     * @phpstan-return array<mixed>
     */
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

            [$a, fn() => Schemas::string()->strlen()->is(11)->up()],
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

    /**
     * @phpstan-return array<mixed>
     */
    public static function provideIntSchema(): array
    {
        $i = 10;

        return [
            [$i, fn() => Schemas::integer()->is($i)],
            [$i, fn() => Schemas::integer()->between($i, $i)],
            [$i, fn() => Schemas::integer()->between($i - 1, $i + 1)],
            [$i, fn() => Schemas::integer()->between($i + 1, $i + 100), false],
            [$i, fn() => Schemas::integer()->between($i - 100, $i - 1), false],


            [$i, fn() => Schemas::integer()->max($i)],
            [$i, fn() => Schemas::integer()->max($i + 1)],
            [$i, fn() => Schemas::integer()->max($i - 1), false],

            [$i, fn() => Schemas::integer()->min($i)],
            [$i, fn() => Schemas::integer()->min($i - 1)],
            [$i, fn() => Schemas::integer()->min($i + 1), false],

            '_100' =>
            [0, fn() => Schemas::integer()->isPositive(), 'validate' => false],
            '_101' =>
            [0, fn() => Schemas::integer()->isPositive(yes: false)],
            '_102' =>
            [0, fn() => Schemas::integer()->isPositive(false)],
            '_103' =>
            [0, fn() => Schemas::integer()->isPositive(false, yes: false), 'validate' =>  false],

            '_105' =>
            [1, fn() => Schemas::integer()->isPositive()],
            '_106' =>
            [1, fn() => Schemas::integer()->isPositive(yes: false), 'validate' =>  false],
            '_107' =>
            [1, fn() => Schemas::integer()->isPositive(false)],
            '_108' =>
            [1, fn() => Schemas::integer()->isPositive(false, yes: false), 'validate' =>  false],

            '_110' =>
            [-1, fn() => Schemas::integer()->isPositive(), 'validate' => false],
            '_111' =>
            [-1, fn() => Schemas::integer()->isPositive(yes: false)],
            '_112' =>
            [-1, fn() => Schemas::integer()->isPositive(false), 'validate' =>  false],
            '_113' =>
            [-1, fn() => Schemas::integer()->isPositive(false, yes: false)],

            '_200' =>
            [0, fn() => Schemas::integer()->isNegative(), 'validate' => false],
            '_201' =>
            [0, fn() => Schemas::integer()->isNegative(yes: false)],
            '_202' =>
            [0, fn() => Schemas::integer()->isNegative(false)],
            '_203' =>
            [0, fn() => Schemas::integer()->isNegative(false, yes: false), 'validate' =>  false],

            '_205' =>
            [1, fn() => Schemas::integer()->isNegative(), 'validate' => false],
            '_206' =>
            [1, fn() => Schemas::integer()->isNegative(yes: false)],
            '_207' =>
            [1, fn() => Schemas::integer()->isNegative(false), 'validate' => false],
            '_208' =>
            [1, fn() => Schemas::integer()->isNegative(false, yes: false)],

            '_210' =>
            [-1, fn() => Schemas::integer()->isNegative()],
            '_211' =>
            [-1, fn() => Schemas::integer()->isNegative(yes: false), 'validate' => false],
            '_212' =>
            [-1, fn() => Schemas::integer()->isNegative(false)],
            '_213' =>
            [-1, fn() => Schemas::integer()->isNegative(false, yes: false), 'validate' =>  false],

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

    /**
     * @phpstan-return array<mixed>
     */
    public static function provideComplexSchema(): array
    {
        /** @phpstan-ignore return.unusedType */
        $fn = fn(int $int): bool|string => true;

        return [
            '_10' =>
            [0, fn() => Schemas::integer()->and(Schemas::string(true)->is('0'))],
            '_11' =>
            [0, fn() => Schemas::integer()->and(Schemas::string(false)->is('0')), false],

            '_100' =>
            [
                10,
                fn() => Schemas::schema()
                    ->integer()->is(10)->up()
                    ->toString()->startsWith('1')->up(),
            ],
            '_101' =>
            [
                10,
                fn() => Schemas::schema()
                    ->integer()->is(10)->up()
                    ->toString()->startsWith('0')->up(),
                false
            ],
            '_102' =>
            [
                10,
                fn() => Schemas::schema()
                    ->toString()->startsWith('1')->up()
                    ->integer()->is(10)->up(),
            ],
            '_103' =>
            [
                10,
                fn() => Schemas::schema()
                    ->toString()->startsWith('0')->up()
                    ->integer()->is(10)->up(),
                false
            ],


            '_500' =>
            [
                $fn,
                fn() => Schemas::closure()->and(Schemas::fromClosure(
                    function (ReflectionFunction $function) {
                        return $function->getShortName() === '{closure}';
                    }
                ))
            ],
            '_501' =>
            [
                $fn,
                fn() => Schemas::closure()->and(Schemas::fromClosure(
                    function (ReflectionFunction $function) {
                        return $function->getShortName() === '{closure}';
                    }
                ))->numberOfParameters()->isPositive()->up()
            ],
            '_501F' =>
            [
                $fn,
                fn() => Schemas::closure()->and(Schemas::fromClosure(
                    function (ReflectionFunction $function) {
                        return $function->getShortName() === '{closure}';
                    }
                ))->numberOfParameters()->isNegative()->up(),
                false
            ],
            '_502' =>
            [
                $fn,
                fn() => Schemas::closure()
                    ->numberOfParameters()->isPositive()->up(returnsClass: AndSchema::class)
                    ->and(Schemas::fromClosure(
                        function (ReflectionFunction $function) {
                            return $function->getShortName() === '{closure}';
                        }
                    ))
            ],
            '_502F' =>
            [
                $fn,
                fn() => Schemas::closure()
                    ->numberOfParameters()->isNegative()->up(returnsClass: AndSchema::class)
                    ->and(Schemas::fromClosure(
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
                    ->and(Schemas::closure()->shortName()->is('{closure}')->up()),
            ],
            '_510F' =>
            [
                $fn,
                fn() => Schemas::closure()
                    ->and(Schemas::closure()->shortName()->is('')->up()),
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
        $a = Schemas::schema();
        $b = $a->and(Schemas::integer());
        $this->assertSame($a, $b);

        $a = Schemas::schema();
        $s = $a->toString()->up(returnsClass: AndSchema::class);
        $b = $s->and(Schemas::integer());
        $this->assertSame($a, $b);
    }

    public function testOpSchemaUnmodifiableChild(): void
    {
        $roota = Schemas::integer();
        $this->assertTrue($roota->validate(100));
        $this->assertFalse($roota->validate('a'));

        // The type of b must be different from a to avoid merging the child list
        $rootb = Schemas::schema()->and($roota);
        $this->assertTrue($rootb->validate(100));
        $this->assertFalse($rootb->validate('a'));

        $roota->is(10);
        // Modifying $roota does not modify $rootb
        $this->assertFalse($roota->validate(100));
        $this->assertTrue($rootb->validate(100));
        $this->assertTrue($roota->validate(10));
        $this->assertTrue($rootb->validate(10));
    }

    // ========================================================================

    /**
     * @phpstan-return array<mixed>
     */
    public static function provideNot(): array
    {
        $testYes1 = fn() => Schemas::schema()
            ->integer()->isPositive()->up()
            ->integer()->is(1)->up();
        $testNot1 = fn() => Schemas::negation()
            ->integer()->isPositive()->up()
            ->integer()->is(1)->up();
        $lTestNot1 = fn() => Schemas::negation(
            Schemas::integer()->isPositive(),
            Schemas::integer()->is(1)
        );

        return [
            [0, fn() => Schemas::integer()->is(0)],
            [0, fn() => Schemas::negation()->integer()->is(0)->up(), false],
            [1, fn() => Schemas::integer()->is(0), false],
            [1, fn() => Schemas::negation()->integer()->is(0)->up()],

            '_10F' =>
            [0, fn() => Schemas::schema()->negationOf(Schemas::integer()->is(0)), false],
            '_10' =>
            [1, fn() => Schemas::schema()->negationOf(Schemas::integer()->is(0))],

            '_100' =>
            [0, $testYes1, false],
            '_101' =>
            [1, $testYes1],
            '_102' =>
            [2, $testYes1, false],

            '_110' =>
            [0, $testNot1],
            '_111' =>
            [1, $testNot1, false],
            '_112' =>
            [2, $testNot1],

            '_110L' =>
            [0, $lTestNot1],
            '_111L' =>
            [1, $lTestNot1, false],
            '_112L' =>
            [2, $lTestNot1],
        ];
    }

    #[DataProvider("provideNot")]
    public function testNot(mixed $element, \Closure $notSchema, bool $validate = true): void
    {
        $schema = $notSchema();
        $this->assertSame($validate, $schema->validate($element));
    }

    // ========================================================================

    /**
     * @phpstan-param Schema[] $andSchemas
     * @phpstan-param mixed[] $values
     * @phpstan-return iterable<mixed[]>
     */
    private static function makeProvidedForAndSchema(
        string $header,
        array $andSchemas,
        array $values,
        bool $validate = true,

    ): iterable {
        $schemas = [
            new Provided("$header:schema", [fn() => Schemas::schema(...$andSchemas)]),
            new Provided("$header:or.intersectionOf", [fn() => Schemas::union()->intersectionOf(...$andSchemas)]),
            new Provided("$header:schema.intersectionOf", [fn() => Schemas::schema()->intersectionOf(...$andSchemas)]),
            new Provided("$header:schema.and", [fn() => Schemas::schema()->and(...$andSchemas)]),
        ];

        $root = Schemas::schema();
        foreach ($andSchemas as $a) {
            $root->and($a);
        }
        $schemas[] = new Provided("$header:*.and", [fn() => $root]);
        return self::makeProvidedForSchemas($schemas, $values, $validate);
    }

    /**
     * @phpstan-return iterable<mixed>
     */
    public static function provideAnd(): iterable
    {
        $ret = [];

        $head = 'positive&^1';
        $schemas =  [
            Schemas::integer()->isPositive(true),
            Schemas::string(true)->startsWith('1')
        ];
        $ret[] =  self::makeProvidedForAndSchema($head, $schemas, [1, 10, 11, 12, 100, 111, 1112]);
        $ret[] =  self::makeProvidedForAndSchema($head, $schemas, [-1, 0, 2, 3, 4, 20], false);

        $head = 'negative&1$';
        $schemas =  [
            Schemas::integer()->isNegative(true),
            Schemas::string(true)->endsWith('2')
        ];
        $ret[] =  self::makeProvidedForAndSchema($head, $schemas, [-2, -12, -22, -102, -122, -1112]);
        $ret[] =  self::makeProvidedForAndSchema($head, $schemas, [1, 0, -1, -3, -4, -20], false);

        return Iterables::append(...$ret);
    }

    #[DataProvider("provideAnd")]
    public function testAnd(mixed $element, \Closure $notSchema, bool $validate = true): void
    {
        $schema = $notSchema();
        $this->assertSame($validate, $schema->validate($element));
    }

    // ========================================================================

    /**
     * @phpstan-param Schema[] $orSchemas
     * @phpstan-param mixed[] $values
     * @phpstan-return iterable<mixed[]>
     */
    private static function makeProvidedForOrSchema(
        string $header,
        array $orSchemas,
        array $values,
        bool $validate = true,
    ): iterable {
        $schemas = [
            new Provided("$header:union", [fn() => Schemas::union(...$orSchemas)]),
            new Provided("$header:and.unionOf", [fn() => Schemas::schema()->unionOf(...$orSchemas)]),
            new Provided("$header:union.unionOf", [fn() => Schemas::union()->unionOf(...$orSchemas)]),
            new Provided("$header:union.or", [fn() => Schemas::union()->or(...$orSchemas)]),
        ];

        $root = Schemas::union();
        foreach ($orSchemas as $a) {
            $root->or($a);
        }
        $schemas[] = new Provided("$header:*.or", [fn() => $root]);

        return self::makeProvidedForSchemas($schemas, $values, $validate);
    }

    /**
     * @phpstan-return iterable<mixed>
     */
    public static function provideOr(): iterable
    {
        $ret = [];

        $head = 'negative|^1';
        $schemas =  [
            Schemas::integer()->isNegative(true),
            Schemas::string(true)->startsWith('1')
        ];
        $ret[] =  self::makeProvidedForOrSchema($head, $schemas, [-1, -2, 1, 10, 11]);
        $ret[] =  self::makeProvidedForOrSchema($head, $schemas, [0, 2, 3, 20, 30], false);

        $head = 'positive|1$';
        $schemas =  [
            Schemas::integer()->isPositive(true),
            Schemas::string(true)->endsWith('2')
        ];
        $ret[] =  self::makeProvidedForOrSchema($head, $schemas, [1, 2, -2, -12, -22]);
        $ret[] =  self::makeProvidedForOrSchema($head, $schemas, [0, -1, -3, -10, -11], false);

        return Iterables::append(...$ret);
    }

    #[DataProvider("provideOr")]
    public function testOr(mixed $element, \Closure $notSchema, bool $validate = true): void
    {
        $schema = $notSchema();
        $this->assertSame($validate, $schema->validate($element));
    }
}
