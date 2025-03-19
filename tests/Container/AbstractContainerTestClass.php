<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use PHPUnit\Framework\Assert;
use Time2Split\Help\Arrays;
use Time2Split\Help\Container\Container;
use Time2Split\Help\Container\ContainerPutMethods;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Iterables;
use Time2Split\Help\Tests\Classes\AbstractClassesTestClass;

/**
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractContainerTestClass extends AbstractClassesTestClass
{
    protected const MIN_NB_ENTRIES = 6;

    abstract protected static function provideContainer(): Container;
    abstract protected static function provideEntries(): array;
    abstract protected static function provideContainerWithSubEntries(int $offset = 0, ?int $length = null): Container;

    // ========================================================================

    protected static final function provideEntryObjects(): array
    {
        $entries = static::provideEntries();
        $toListOfEntry = function (array|entry $entry) {

            if (\is_array($entry)) {
                Assert::assertCount(1, $entry);
                return new Entry(Arrays::firstKey($entry), Arrays::firstValue($entry));
            } else
                return $entry;
        };
        return \array_map($toListOfEntry, $entries);
    }

    protected static final function provideSubEntries(int $offset = 0, ?int $length = null): array
    {
        return \array_slice(static::provideEntryObjects(), $offset, $length, true);
    }

    // ========================================================================

    public static function setUpBeforeClass(): void
    {
        $entries = static::provideEntryObjects();

        if (\count($entries) < self::MIN_NB_ENTRIES)
            \fprintf(STDERR, "static::provideEntryObjects() bad format (count)");
    }

    // ========================================================================

    #[\Override]
    protected static final function provideSubject(): Container
    {
        return static::provideContainer();
    }

    // ========================================================================

    public final function testEmptyContainer(): void
    {
        $subject = static::provideContainer();
        $this->checkEmpty($subject);
    }

    final public function testCopyableContainer(): void
    {
        $subject = static::provideContainerWithSubEntries();
        $copy = $subject->copy();
        $this->checkNotEmpty($copy);
        $this->checkListEquals($subject, $copy);
    }

    final public function testClearableContainer(): void
    {
        $subject = static::provideContainerWithSubEntries();
        $entries = $this->provideSubEntries();
        $this->checkNotEmpty($subject, \count($entries));
        $subject->clear();
        $this->checkEmpty($subject);
    }

    final public function testPutMethodsContainer(): void
    {
        $subject = static::provideContainer();

        if (!($subject instanceof ContainerPutMethods))
            $this->markTestSkipped("Not a ContainerPutMethods");

        $a = fn() => Entry::traverseListOfEntries(static::provideSubEntries(0, 3));
        $b = fn() => Entry::traverseListOfEntries(static::provideSubEntries(3, 3));
        $array = fn() => Iterables::append($a(), $b());

        $subject->putMore(...Iterables::keys($a()));
        $this->checkNotEmpty($subject, 3);
        $this->checkValuesEqualsProvidedEntries($subject, $a());

        $subject->putMore(...Iterables::keys($b()));
        $this->checkNotEmpty($subject, 6);
        $this->checkEqualsProvidedEntries($subject, $array());
    }

    final public function testToArrayContainer(): void
    {
        $subject = static::provideContainerWithSubEntries();
        $entries = $this->provideSubEntries();
        $this->checkEqualsProvidedEntries($subject->toArray(), $entries);
        $this->checkEqualsProvidedEntries($subject->toArrayContainer(), $entries);
    }

    final public function testTraversableContainer(): void
    {
        $subject = static::provideContainerWithSubEntries();
        $entries = fn() => Entry::traverseEntries($this->provideSubEntries());
        $this->checkValuesEqualsProvidedEntries($subject, $entries());
    }
}
