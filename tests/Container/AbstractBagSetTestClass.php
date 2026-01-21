<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use Time2Split\Help\Container\Entry;

/**
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractBagSetTestClass extends AbstractArrayAccessContainerTestClass
{
    #[\Override]
    protected function checkEqualsProvidedEntries(iterable $subject, iterable $entries, bool $strict = false): void
    {
        $expect = Entry::traverseEntries($entries);
        parent::checkEntriesAreEqual($subject, $expect, $strict);
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
