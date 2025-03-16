<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Set;

use PHPUnit\Framework\TestCase;
use Time2Split\Help\Set;
use Time2Split\Help\Sets;
use Time2Split\Help\Tests\Container\BagSetTestTrait;
use Time2Split\Help\Tests\Resource\AUnitEnum;
use Time2Split\Help\Tests\Resource\BackedStringEnum;

/**
 * @author Olivier Rodriguez (zuri)
 */
class SetOfEnumTest extends TestCase
{
    use BagSetTestTrait;

    #[\Override]
    protected final function provideContainer(): Set
    {
        return $this->provideContainerOfEnumType($this->provideEnumType());
    }

    #[\Override]
    protected final function provideOneItem(): mixed
    {
        return $this->provideEnumType()::a;
    }

    #[\Override]
    protected final function provideOneUnexistantItem(): mixed
    {
        return $this->provideEnumType()::f;
    }

    #[\Override]
    protected final function provideListsForThreeItems(): array
    {
        $enumType = $this->provideEnumType();
        return [[$enumType::d], [$enumType::b], [$enumType::c, $enumType::d]];
    }

    // ========================================================================
    // To be override

    public function provideContainerOfEnumType($enumType): Set
    {
        return Sets::ofEnum($enumType);
    }

    public function provideEnumType(): string
    {
        return AUnitEnum::class;
    }

    // ========================================================================

    public final function testFromInstance()
    {
        $subject = $this->provideContainer();
        $enumType = $this->provideEnumType();

        $this->assertInstanceOf(Set::class, $subject);
        $subject[$enumType::a] = true;
        $this->checkItemExists($subject, $enumType::a);
    }

    public final function testInvalidCase()
    {
        $subject = $this->provideContainer();
        $this->expectException(\InvalidArgumentException::class);
        $subject[BackedStringEnum::a] = true;
    }
}
