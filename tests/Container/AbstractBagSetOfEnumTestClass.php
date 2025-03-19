<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use Time2Split\Help\Container\Bag;
use Time2Split\Help\Container\Set;
use Time2Split\Help\Tests\Container\Set\AbstractSetTestClass;
use Time2Split\Help\Tests\Resource\AUnitEnum;
use Time2Split\Help\Tests\Resource\BackedStringEnum;

/**
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractBagSetOfEnumTestClass extends AbstractSetTestClass
{
    #[\Override]
    protected static function provideContainer(): Bag|Set
    {
        return static::provideContainerOfEnumType(static::provideEnumType());
    }

    // ========================================================================
    // To be override

    abstract protected static function provideContainerOfEnumType($enumType): Bag|Set;

    protected static function provideEnumType(): string
    {
        return AUnitEnum::class;
    }

    protected static function provideBadEnumType(): string
    {
        return BackedStringEnum::class;
    }

    // ========================================================================

    public final function testFromInstanceBagSet()
    {
        $enumType = static::provideEnumType();
        $subject = static::provideContainerOfEnumType($enumType::a);
        $subject[$enumType::a] = true;
        $this->checkOffsetExists($subject, $enumType::a);
    }

    public final function testInvalidCaseBagSet()
    {
        $subject = static::provideContainer();
        $this->expectException(\InvalidArgumentException::class);
        $enum = static::provideBadEnumType();
        $subject[$enum::a] = true;
    }
}
