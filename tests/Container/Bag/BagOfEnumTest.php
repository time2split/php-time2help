<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Bag;

use PHPUnit\Framework\TestCase;
use Time2Split\Help\Container\Bag;
use Time2Split\Help\Container\Bags;
use Time2Split\Help\Tests\Resource\AUnitEnum;
use Time2Split\Help\Tests\Resource\BackedStringEnum;

/**
 * @author Olivier Rodriguez (zuri)
 */
class BagOfEnumTest extends TestCase
{
    use BagTestTrait;

    #[\Override]
    protected final function provideContainer(): Bag
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

    public function provideContainerOfEnumType($enumType): Bag
    {
        return Bags::ofEnum($enumType);
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

        $this->assertInstanceOf(Bag::class, $subject);
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
