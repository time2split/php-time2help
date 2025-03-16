<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Trait;

use ArrayAccess;
use Countable;
use Time2Split\Help\Functions;
use Time2Split\Help\Iterables;
use Time2Split\Help\Tests\DiffReports;
use Traversable;

/**
 * @author Olivier Rodriguez (zuri)
 */
trait ArrayAccessUtils
{
    abstract protected function provideOneUnexistantItem(): mixed;

    protected function itemToString($item): string
    {
        return Functions::basicToString($item);
    }

    protected final function checkNotEmpty(ArrayAccess $subject, ?int $count = null)
    {
        $this->assertNotEmpty($subject);

        if ($subject instanceof Countable) {

            if (null === $count) {
                $this->assertNotCount(0, $subject);
                $count = \count($subject);
            }
            $this->checkCountEquals($subject, $count);
        }
        $this->checkItemNotExists($subject, $this->provideOneUnexistantItem());
    }

    protected final function checkEmpty(ArrayAccess $subject)
    {
        $this->assertEmpty($subject);

        if ($subject instanceof Countable)
            $this->checkCountEquals($subject, 0);

        $this->checkItemNotExists($subject, $this->provideOneUnexistantItem());
    }

    protected final function checkItemNotExists(ArrayAccess $subject, $item)
    {
        $h = "checkItemNotExists/";
        $item_s = $this->itemToString($item);
        $this->assertFalse($subject[$item], "{$h}offsetGet($item_s)");
        $this->assertFalse($subject->offsetExists($item), "{$h}offsetExists($item_s)");
        $this->assertFalse(isset($subject[$item]), "{$h}isset($item_s)");
    }

    protected final function checkItemExists(ArrayAccess $subject, $item)
    {
        $h = "checkItemExists/";
        $item_s = $this->itemToString($item);
        $this->assertTrue($subject[$item], "{$h}offsetGet($item_s)");
        $this->assertTrue($subject->offsetExists($item), "{$h}offsetExists($item_s)");
        $this->assertTrue(isset($subject[$item]), "{$h}isset($item_s)");
    }

    protected final function checkCountEquals(Countable $subject, int $count)
    {
        $this->assertCount($count, $subject);

        if ($subject instanceof Traversable) {
            $array = \iterator_to_array($subject);
            $this->assertCount($count, $array);
        }
    }

    protected final function checkIterableItems(iterable $subject, iterable $expect)
    {
        $array = \iterator_to_array($subject);
        $check = Iterables::valuesEquals($expect, $array);

        if (!$check) {
            $diff = DiffReports::listTextReport($expect, $array);
            $this->fail("{\n$diff}");
        }
        $this->assertTrue(true);
    }
}
