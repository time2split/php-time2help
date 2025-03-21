<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests;

use Countable;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Iterables;
use Time2Split\Help\Tests\DiffReports;
use Time2Split\Help\Tests\Resource\Constraint\IsInstanceOfObject;
use Traversable;

/**
 * @author Olivier Rodriguez (zuri)
 */
trait TestUtils
{
    protected function checkInstanceOf(string|object $expectedClass, mixed $actual, string $message = ''): void
    {
        if (\is_string($expectedClass))
            $this->assertInstanceOf($expectedClass, $actual, $message);
        elseif (!($expectedClass instanceof $actual)) {
            self::assertThat(
                $actual,
                new IsInstanceOfObject($expectedClass),
                $message,
            );
        }
    }

    protected function checkNotEmpty(mixed $subject, ?int $count = null): void
    {
        $this->assertNotEmpty($subject);

        if ($subject instanceof Countable) {

            if (null === $count) {
                $this->assertNotCount(0, $subject);
            } else {
                $this->checkCountEquals($subject, $count);
            }
        }
    }

    protected function checkEmpty(mixed $subject): void
    {
        $this->assertEmpty($subject);

        if ($subject instanceof Countable)
            $this->checkCountEquals($subject, 0);
    }

    protected function checkCountEquals(Countable|array $subject, int $count): void
    {
        $this->assertCount($count, $subject);

        if ($subject instanceof Traversable) {
            $countArray = ITerables::count($subject);
            $this->assertSame($count, $countArray, "Array size");
        }
    }

    protected function checkValuesEqualsProvidedEntries(iterable $subject, iterable $entries, bool $strict = false): void
    {
        $eit = $entries;
        $eit->rewind();

        foreach ($subject as $k => $v) {
            $this->assertTrue($eit->valid());
            $this->checkEntryValueEqualsProvidedEntry(
                new Entry($k, $v),
                new Entry($eit->key(), $eit->current()),
                $strict
            );
            $eit->next();
        }
        $this->assertFalse($eit->valid());
    }

    protected function checkEntryValueEqualsProvidedEntry(Entry $subject, Entry $expect, bool $strict = false): void
    {
        if ($strict)
            $this->assertSame($subject->value, $expect->value);
        else
            $this->assertEquals($subject->value, $expect->value);
    }


    protected function checkListEquals(iterable $subject, iterable $expect, bool $strict = false): void
    {
        $expect_a = \iterator_to_array(Entry::toTraversableEntries($expect));
        $subject_a = \iterator_to_array(Entry::toTraversableEntries($subject));

        $this->checkCountEquals($subject_a, \count($expect_a));

        $check = Iterables::valuesEquals($expect_a, $subject_a, $strict);
        if (!$check) {
            $diff = DiffReports::listTextReport($expect_a, $subject_a);
            $this->fail("{\n$diff}");
        }
    }

    protected function checkIterablesEquals(iterable $subject, iterable $expect, bool $strict = false): void
    {
        $check = Iterables::sequenceEquals($subject, $expect, $strict, $strict);
        $this->assertTrue($check);
    }

    protected function checkEqualsProvidedEntries(iterable $subject, iterable $entries, bool $strict = false): void
    {
        $this->checkListEquals($subject, $entries, $strict);
    }
}
