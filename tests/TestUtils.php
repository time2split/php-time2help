<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests;

use Closure;
use Countable;
use Time2Split\Diff\Algorithm\Myers;
use Time2Split\Diff\DiffInstructionType;
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
    protected final function checkInstanceOf(string|object $expectedClass, mixed $actual, string $message = ''): void
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

    protected final function checkNotEmpty(mixed $subject, ?int $count = null): void
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

    protected final function checkEmpty(mixed $subject): void
    {
        $this->assertEmpty($subject);

        if ($subject instanceof Countable)
            $this->checkCountEquals($subject, 0);
    }

    protected final function checkCountEquals(Countable|array $subject, int $count): void
    {
        $this->assertCount($count, $subject);

        if ($subject instanceof Traversable) {
            $countArray = ITerables::count($subject);
            $this->assertSame($count, $countArray, "Array size");
        }
    }

    protected final function checkEntriesAreEqual(iterable $subject, iterable $expect, null|bool|Closure $equals = null): void
    {
        if (null === $equals)
            $equals = Entry::equalsClosure(false);
        elseif (\is_bool($equals))
            $equals = Entry::equalsClosure($equals);

        $expect_a = \iterator_to_array(Entry::toTraversableEntries($expect));
        $subject_a = \iterator_to_array(Entry::toTraversableEntries($subject));

        $this->checkCountEquals($subject_a, \count($expect_a));

        $diff = Myers::diffList($expect_a, $subject_a, $equals);

        $fail = \array_any($diff, fn($i) => $i->type !== DiffInstructionType::Keep);

        if ($fail) {
            $text = DiffReports::textReportOfList($diff);
            $this->fail("$text\n");
        }
        $this->assertTrue(true);
    }

    protected final function checkIterablesEquals(iterable $subject, iterable $expect, bool $strict = false): void
    {
        $check = Iterables::sequenceEquals($subject, $expect, $strict, $strict);
        $this->assertTrue($check);
    }

    protected function checkEqualsProvidedEntries(iterable $subject, iterable $entries, bool $strict = false): void
    {
        $this->checkEntriesAreEqual($subject, $entries, $strict);
    }
}
