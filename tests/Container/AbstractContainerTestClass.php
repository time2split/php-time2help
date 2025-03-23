<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use Closure;
use PHPUnit\Framework\Assert;
use Time2Split\Help\Arrays;
use Time2Split\Help\Container\FetchingClosed;
use Time2Split\Help\Container\ContainerPutMethods;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Container\FetchingOpened;
use Time2Split\Help\Iterables;
use Time2Split\Help\Tests\Classes\AbstractClassesTestClass;

/**
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractContainerTestClass extends AbstractClassesTestClass
{
    protected const MIN_NB_ENTRIES = 6;

    abstract protected static function provideContainer(): mixed;
    abstract protected static function provideEntries(): array;
    abstract protected static function provideContainerWithSubEntries(int $offset = 0, ?int $length = null);

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

    protected static function entriesEqualClosure_traversableTest(bool $strict = false): Closure
    {
        return Entry::equalsClosure($strict);
    }

    protected static function entriesEqualClosure_putMethodTest(bool $strict = false): Closure
    {
        return Entry::equalsClosure($strict);
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
    protected static final function provideSubject(): mixed
    {
        return static::provideContainer();
    }

    // ========================================================================

    public final function testEmptyContainer(): void
    {
        $subject = static::provideContainer();
        $this->checkEmpty($subject);
    }

    final public function testFetchingContainer(): void
    {
        $subject = static::provideContainer();
        $this->assertTrue(
            ($subject instanceof FetchingOpened) xor ($subject instanceof FetchingClosed)
        );
    }

    final public function testCopyableContainer(): void
    {
        $subject = static::provideContainerWithSubEntries();
        $copy = $subject->copy();
        $this->checkNotEmpty($copy);
        $this->checkEntriesAreEqual($subject, $copy);
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

        $subject->putMore(...\iterator_to_array(Iterables::keys($a())));
        $this->checkNotEmpty($subject, 3);
        $this->checkEntriesAreEqual($subject, $a(), static::entriesEqualClosure_putMethodTest());

        $subject->putFromList(Iterables::keys($b()));
        $this->checkNotEmpty($subject, 6);
        $this->checkEntriesAreEqual($subject, $array(), static::entriesEqualClosure_putMethodTest());
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
        $this->checkEntriesAreEqual($subject, $entries(), static::entriesEqualClosure_traversableTest());
    }

    // ========================================================================
    // FETCHING OPENED

    final public function testFetchingOpenedEqualsContainer(): void
    {
        /**
         * @var Trait\FetchingOpened
         */
        $a = static::provideContainerWithSubEntries();
        if (!($a instanceof FetchingOpened))
            $this->markTestSkipped();

        $eq = fn($a, $b) => $a == $b;
        $this->assertTrue($a->equals($a, true), 'a === a');
        $this->assertTrue($a->equals($a, false), 'a == a');
        $this->assertTrue($a->equals($a, $eq), 'fn:a == a');

        /**
         * @var Trat\FetchingOpened
         */
        $b = static::provideContainerWithSubEntries();
        $this->assertInstanceOf(FetchingOpened::class, $b);
        $this->assertSame($a->count(), $b->count(), '|a| === |b|');

        $this->assertTrue($a->equals($b, true), 'a === b');
        $this->assertTrue($b->equals($a, true), 'b === a');
        $this->assertTrue($a->equals($b, false), 'a == b');
        $this->assertTrue($b->equals($a, false), 'b == a');
        $this->assertTrue($a->equals($b, $eq), 'fn:a == b');
        $this->assertTrue($b->equals($a, $eq), 'fn:b == a');

        // Not equals
        $b = static::provideContainerWithSubEntries(length: -1);
        $this->assertInstanceOf(FetchingOpened::class, $b);
        $this->assertSame($a->count(), $b->count() + 1, '|a| === |b|+1');

        $this->assertFalse($a->equals($b, true), '(!) a === b');
        $this->assertFalse($a->equals($b, false), '(! )a == b');
        $this->assertFalse($a->equals($b, $eq), '(!) fn:a == b');
    }

    final public function testFetchingOpenedIncludesInContainer(): void
    {
        /**
         * @var Trait\Fetching
         */
        $a = static::provideContainerWithSubEntries(length: -1);
        if (!($a instanceof FetchingOpened))
            $this->markTestSkipped();

        $eq = fn($a, $b) => $a == $b;
        $this->assertTrue($a->isIncludedIn($a, true), '(===) a <= a');
        $this->assertTrue($a->isIncludedIn($a, true), '(==) a <= a');
        $this->assertTrue($a->isIncludedIn($a, $eq), 'fn:a <= a');

        /**
         * @var Trat\FetchingOpened
         */
        $b = static::provideContainerWithSubEntries();
        $this->assertInstanceOf(FetchingOpened::class, $b);
        $this->assertSame($a->count(), $b->count() - 1, '|a| === |b|-1');

        $this->assertTrue($a->isIncludedIn($b, true), '(===) a <= b');
        $this->assertFalse($b->isIncludedIn($a, true), '(!) b <= a');
        $this->assertTrue($a->isIncludedIn($b, false), '(==) a <= b');
        $this->assertFalse($b->isIncludedIn($a, false), '(!)(==) b <= a');
        $this->assertTrue($a->isIncludedIn($b, $eq), 'fn:a <= b');
        $this->assertFalse($b->isIncludedIn($a, $eq), '(!)fn:b <= a');

        // Equals inclusion
        /**
         * @var Trat\FetchingOpened
         */
        $b = static::provideContainerWithSubEntries(length: -1);
        $this->assertInstanceOf(FetchingOpened::class, $b);
        $this->assertSame($a->count(), $b->count(), '|a| === |b|');

        $this->assertTrue($a->isIncludedIn($b, true), '(a.eq(b)) a <= b');
        $this->assertTrue($b->isIncludedIn($a, true), '(a.eq(b)) b <= a');
        $this->assertTrue($a->isIncludedIn($b, false), '(a.eq(b)) a <= b');
        $this->assertTrue($b->isIncludedIn($a, false), '(a.eq(b)) b <= a');
        $this->assertTrue($a->isIncludedIn($b, $eq), '(a.eq(b)) fn:a <= b');
        $this->assertTrue($b->isIncludedIn($a, $eq), '(a.eq(b)) fn:b <= a');
    }

    final public function testFetchingOpenedIncludesInContainerStrict(): void
    {
        $strict = true;
        /**
         * @var Trait\FetchingOpened
         */
        $a = static::provideContainerWithSubEntries(length: -1);
        if (!($a instanceof FetchingOpened))
            $this->markTestSkipped();

        $eq = fn($a, $b) => $a == $b;
        $this->assertFalse($a->isIncludedIn($a, true, $strict), '(!)(===) a <= a');
        $this->assertFalse($a->isIncludedIn($a, true, $strict), '(!)(==) a <= a');
        $this->assertFalse($a->isIncludedIn($a, $eq, $strict), '(!)fn:a <= a');

        /**
         * @var Trat\FetchingOpened
         */
        $b = static::provideContainerWithSubEntries();
        $this->assertInstanceOf(FetchingOpened::class, $b);
        $this->assertSame($a->count(), $b->count() - 1, '|a| === |b|-1');

        $this->assertTrue($a->isIncludedIn($b, true,  $strict), '(===) a < b');
        $this->assertFalse($b->isIncludedIn($a, true,  $strict), '(!) b < a');
        $this->assertTrue($a->isIncludedIn($b, false,  $strict), '(==) a < b');
        $this->assertFalse($b->isIncludedIn($a, false,  $strict), '(!)(==) b < a');
        $this->assertTrue($a->isIncludedIn($b, $eq,  $strict), 'fn:a < b');
        $this->assertFalse($b->isIncludedIn($a, $eq,  $strict), '(!)fn:b < a');

        // Equals inclusion
        /**
         * @var Trat\FetchingOpened
         */
        $b = static::provideContainerWithSubEntries(length: -1);
        $this->assertInstanceOf(FetchingOpened::class, $b);
        $this->assertSame($a->count(), $b->count(), '|a| === |b|');

        $this->assertFalse($a->isIncludedIn($b, true,  $strict), '(!)(a.eq(b)) a < b');
        $this->assertFalse($b->isIncludedIn($a, true,  $strict), '(!)(a.eq(b)) b < a');
        $this->assertFalse($a->isIncludedIn($b, false,  $strict), '(!)(a.eq(b)) a < b');
        $this->assertFalse($b->isIncludedIn($a, false,  $strict), '(!)(a.eq(b)) b < a');
        $this->assertFalse($a->isIncludedIn($b, $eq,  $strict), '(!)(a.eq(b)) fn:a < b');
        $this->assertFalse($b->isIncludedIn($a, $eq,  $strict), '(!)(a.eq(b)) fn:b < a');
    }
    // ========================================================================

    final public function testFetchingClosedEqualsContainer(): void
    {
        /**
         * @var Trait\FetchingClosed
         */
        $a = static::provideContainerWithSubEntries();
        if (!($a instanceof FetchingClosed))
            $this->markTestSkipped();

        $this->assertTrue($a->equals($a), 'a == a');

        /**
         * @var Trait\FetchingClosed
         */
        $b = static::provideContainerWithSubEntries();
        $this->assertInstanceOf(FetchingClosed::class, $b);
        $this->assertSame($a->count(), $b->count(), '|a| === |b|');

        $this->assertTrue($a->equals($b), 'a == b');
        $this->assertTrue($b->equals($a), 'b == a');

        // Not equals
        $b = static::provideContainerWithSubEntries(length: -1);
        $this->assertInstanceOf(FetchingClosed::class, $b);
        $this->assertSame($a->count(), $b->count() + 1, '|a| === |b|+1');

        $this->assertFalse($a->equals($b), '(!)a == b');
        $this->assertFalse($b->equals($a), '(!)b == a');
    }

    final public function testFetchingClosedIncludesInContainer(): void
    {
        /**
         * @var Trait\FetchingClosed
         */
        $a = static::provideContainerWithSubEntries(length: -1);
        if (!($a instanceof FetchingClosed))
            $this->markTestSkipped();

        $this->assertTrue($a->isIncludedIn($a), 'a <= a');

        /**
         * @var Trait\FetchingClosed
         */
        $b = static::provideContainerWithSubEntries();
        $this->assertInstanceOf(FetchingClosed::class, $b);
        $this->assertSame($a->count(), $b->count() - 1, '|a| === |b|-1');

        $this->assertTrue($a->isIncludedIn($b), 'a <= b');
        $this->assertFalse($b->isIncludedIn($a), '(!) b <= a');

        // Equals inclusion
        /**
         * @var Trait\FetchingClosed
         */
        $b = static::provideContainerWithSubEntries(length: -1);
        $this->assertInstanceOf(FetchingClosed::class, $b);
        $this->assertSame($a->count(), $b->count(), '|a| === |b|');

        $this->assertTrue($a->isIncludedIn($b), '(a.eq(b)) a <= b');
        $this->assertTrue($b->isIncludedIn($a), '(a.eq(b)) b <= a');
    }

    final public function testFetchingClosedIncludesInContainerStrict(): void
    {
        $this->markTestSkipped("disabled");
        $strict = true;
        /**
         * @var Trait\FetchingClosed
         */
        $a = static::provideContainerWithSubEntries(length: -1);
        if (!($a instanceof FetchingClosed))
            $this->markTestSkipped();

        $this->assertFalse($a->isIncludedIn($a,  $strict), '(!) a <= a');

        /**
         * @var Trat\FetchingClosed
         */
        $b = static::provideContainerWithSubEntries();
        $this->assertInstanceOf(FetchingClosed::class, $b);
        $this->assertSame($a->count(), $b->count() - 1, '|a| === |b|-1');

        $this->assertTrue($a->isIncludedIn($b,  $strict), 'a < b');
        $this->assertFalse($b->isIncludedIn($a,  $strict), '(!) b < a');

        // Equals inclusion
        /**
         * @var Trat\FetchingClosed
         */
        $b = static::provideContainerWithSubEntries(length: -1);
        $this->assertInstanceOf(FetchingClosed::class, $b);
        $this->assertSame($a->count(), $b->count(), '|a| === |b|');

        $this->assertFalse($a->isIncludedIn($b, $strict), '(!)(a.eq(b)) a < b');
        $this->assertFalse($b->isIncludedIn($a, $strict), '(!)(a.eq(b)) b < a');
    }
}
