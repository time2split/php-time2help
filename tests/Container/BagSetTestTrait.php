<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use Time2Split\Help\Bag;
use Time2Split\Help\Exception\UnmodifiableSetException;
use Time2Split\Help\Functions;
use Time2Split\Help\Set;
use Time2Split\Help\Sets;
use Time2Split\Help\Tests\Trait\ArrayAccessUtils;

/**
 * @author Olivier Rodriguez (zuri)
 */
trait BagSetTestTrait
{
    use ArrayAccessUtils;

    abstract protected function provideContainer(): Set|Bag;
    abstract protected function provideOneItem(): mixed;
    abstract protected function provideListsForThreeItems(): array;

    protected final function provideThreeItems(): array
    {
        return \array_merge(...$this->provideListsForThreeItems());
    }

    protected final function provideThreeItemsUnordered(): array
    {
        $items = $this->provideThreeItems();
        [$items[1], $items[2]] =  [$items[2], $items[1]];
        return $items;
    }

    private function provideOneItemContainer(): array
    {
        $subject = $this->provideContainer();
        $item = $this->provideOneItem();
        $subject[$item] =  true;
        return [
            $item,
            $subject
        ];
    }

    // ========================================================================

    public function testThreeItemsPattern(): void
    {
        // Check the pattern of the items
        $items = $this->provideThreeItems();
        $text = Functions::basicToString($items);

        $this->assertCount(4, $items, $text);

        $map = fn($i) => match ($i) {
            $items[0] => 0,
            $items[1] => 1,
            $items[2] => 2,
            $items[3] => 0,
        };
        $pattern = \array_map($map, $items);
        $expect = [0, 1, 2, 0];
        $this->assertSame($expect, $pattern, $text);
    }

    // ========================================================================

    public final function testEmpty(): void
    {
        $subject = $this->provideContainer();
        $this->checkEmpty($subject);
    }

    // ========================================================================

    public final function testPutOne(): void
    {
        $subject = $this->provideContainer();
        $item = $this->provideOneItem();
        $subject[$item] = true;
        $this->checkNotEmpty($subject, 1);
        $this->checkItemExists($subject, $item);
    }

    public final function testCopy(): void
    {
        [$item, $subject] = $this->provideOneItemContainer();
        $copy = $subject->copy();
        $this->assertNotSame($copy, $subject);
        $this->checkNotEmpty($copy, 1);
        $this->checkItemExists($copy, $item);
    }

    public final function testPutOneDuplicate(): void
    {
        [$item, $subject] = $this->provideOneItemContainer();
        $subject[$item] = true;
        $this->checkNotEmpty($subject, 1);
        $this->checkItemExists($subject, $item);
    }

    public final function testDropOne(): void
    {
        [$item, $subject] = $this->provideOneItemContainer();
        $subject[$item] = false;
        $this->checkEmpty($subject);
    }

    // ========================================================================

    public final function testPutMore(): void
    {
        [$item, $subject] = $this->provideOneItemContainer();
        $items = $this->provideThreeItems();

        $subject->setMore(...$items);
        $this->checkNotEmpty($subject, 4);
        $this->checkItemExists($subject, $item);

        foreach ($items as $i)
            $this->checkItemExists($subject, $i);

        $expect = \array_unique([$item, ...$items], SORT_REGULAR);
        $this->checkIterableItems($subject, $expect);
    }

    public final function testPutMoreFromList(): void
    {
        [$item, $subject] = $this->provideOneItemContainer();
        $items = $this->provideListsForThreeItems();
        $flatItems = \array_merge(...$items);

        $subject->setFromList(...$items);
        $this->checkNotEmpty($subject, 4);
        $this->checkItemExists($subject, $item);

        foreach ($flatItems as $i)
            $this->checkItemExists($subject, $i);

        $expect = \array_unique([$item, ...$flatItems], SORT_REGULAR);
        $this->checkIterableItems($subject, $expect);
    }

    public function testUnmodifiable(): void
    {
        [$item, $subject] = $this->provideOneItemContainer();
        $unm = Sets::unmodifiable($subject);
        $this->checkNotEmpty($unm, 1);
        $this->checkItemExists($unm, $item);
        $this->expectException(UnmodifiableSetException::class);
        // Even if it does nothing (no modification), no call to a setter is allowed
        $unm[$item] = true;
    }

    // ========================================================================

    public function testEquals(): void
    {
        $a = $this->provideContainer();
        $a->setFromList($this->provideThreeItems());
        $b = $a->copy();

        $this->assertTrue(Sets::equals($a, $b));
        $absentItem = $this->provideOneUnexistantItem();

        // Must be order independant
        $b = $this->provideContainer();
        $b->setFromList($this->provideThreeItemsUnordered());
        $this->assertTrue(Sets::equals($a, $b), 'Order dependency');
        $this->assertTrue(Sets::equals($b, $a), 'Order dependency');

        $b[$absentItem] = true;
        $this->assertFalse(Sets::equals($a, $b));
    }

    public function testIncludedIn()
    {
        $items = $this->provideThreeItems();
        $a = $this->provideContainer();
        $a->setFromList($items);
        $b = $a->copy();

        $this->assertTrue(Sets::includedIn($a, $b), 'Not the sames');
        $this->assertTrue(Sets::includedIn($b, $a), 'Not the sames');

        // Must be order independant
        $b = $this->provideContainer();
        $b->setFromList($this->provideThreeItemsUnordered());
        $this->assertTrue(Sets::includedIn($a, $b), 'Order dependency');
        $this->assertTrue(Sets::includedIn($b, $a), 'Order dependency');

        $items2 = $items;
        unset($items2[1]);
        $a = $this->provideContainer();
        $a->setFromList($items2);
        $this->assertTrue(Sets::includedIn($a, $b), 'a < b');
        $this->assertFalse(Sets::includedIn($b, $a), 'b < a');

        $a->setMore($this->provideOneUnexistantItem());
        $this->assertFalse(Sets::includedIn($a, $b), 'a < b');
        $this->assertFalse(Sets::includedIn($b, $a), 'b < a');
    }
}
