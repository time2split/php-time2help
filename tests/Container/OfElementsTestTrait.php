<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

trait OfElementsTestTrait
{
    abstract  protected static function provideElements(): array;

    protected static final function provideSubElements(int $offset = 0, ?int $length = null): array
    {
        return \array_slice(self::provideElements(), $offset, $length);
    }

    // ========================================================================

    public final function testElements()
    {
        $subject = $this->provideContainerWithSubEntries();
        $expect = $this->provideSubElements();
        $this->assertSame($expect, \iterator_to_array($subject->elements()));

        $selements = $subject->toListOfElements();
        $this->assertSame($expect, $selements);
    }
}
