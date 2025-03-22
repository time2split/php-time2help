<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use Closure;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Iterables;

/**
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractBagSetTestClass extends AbstractArrayAccessContainerTestClass
{
    #[\Override]
    protected function checkEqualsProvidedEntries(iterable $subject, iterable $entries, bool $strict = false): void
    {
        $expect = Iterables::keys(Entry::traverseEntries($entries));
        parent::checkEntriesAreEqual($subject, $expect, $strict);
    }

    #[\Override]
    protected static function entriesEqualClosure_traversableTest(
        bool $strict = false
    ): Closure {
        $eq = Entry::equalsClosure($strict);
        return fn(
            Entry $subject,
            Entry $expect,
        ) =>  $eq($expect->flip()->setValue(1), $subject);
    }

    #[\Override]
    protected static function entriesEqualClosure_putMethodTest(bool $strict = false): Closure
    {
        $eq = Entry::equalsClosure($strict);
        return fn(
            Entry $subject,
            Entry $expect,
        ) =>  $eq($expect->flip()->setValue(1), $subject);
    }

    // ========================================================================

    public final function testSetUpdate(): void
    {
        $subject = $this->provideContainer();
        $entries = $this->provideEntryObjects();

        $subject[($e = $entries[0])->key] = true;
        $this->checkNotEmpty($subject, 1);
        $this->checkOffsetValue($subject, $e->key, $e->value, false);

        $subject[($e = $entries[1])->key] = true;
        $this->checkNotEmpty($subject, 2);
        $this->checkOffsetValue($subject, $e->key, $e->value, false);

        $subject[$entries[2]->key] = false;
        $this->checkNotEmpty($subject, 2);

        $subject[($e = $entries[1])->key] = false;
        $this->checkNotEmpty($subject, 1);
        $this->checkOffsetNotExists($subject, $e->key);

        $subject[($e = $entries[0])->key] = false;
        $this->checkEmpty($subject);
    }
}
