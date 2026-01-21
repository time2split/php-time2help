<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use Time2Split\Help\Container\Bag;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Container\Set;
use Time2Split\Help\TriState;

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

    public final function testEquals(): void
    {
        $subject = $this->provideContainerWithSubEntries();
        assert($subject instanceof Bag || $subject instanceof Set);
        $this->assertTrue($subject->equals($subject));

        $subjectCopy = $subject->copy();
        $this->assertTrue($subject->equals($subjectCopy));
        $this->assertTrue($subjectCopy->equals($subject));

        $lessSubject = $this->provideContainerWithSubEntries(1);
        assert($lessSubject instanceof Bag || $lessSubject instanceof Set);
        $this->assertFalse($subject->equals($lessSubject));
        $this->assertFalse($lessSubject->equals($subject));
    }

    public final function testIsIncludedIn(): void
    {
        $subject = $this->provideContainerWithSubEntries();
        assert($subject instanceof Bag || $subject instanceof Set);
        $this->assertTrue($subject->isIncludedIn($subject, TriState::No));
        $this->assertTrue($subject->isIncludedIn($subject, TriState::Maybe));
        $this->assertFalse($subject->isIncludedIn($subject, TriState::Yes));

        $subjectCopy = $subject->copy();
        $this->assertTrue($subject->isIncludedIn($subjectCopy, TriState::No));
        $this->assertTrue($subject->isIncludedIn($subjectCopy, TriState::Maybe));
        $this->assertFalse($subject->isIncludedIn($subjectCopy, TriState::Yes));

        $this->assertTrue($subjectCopy->isIncludedIn($subject, TriState::No));
        $this->assertTrue($subjectCopy->isIncludedIn($subject, TriState::Maybe));
        $this->assertFalse($subjectCopy->isIncludedIn($subject, TriState::Yes));


        $lessSubject = $this->provideContainerWithSubEntries(1);
        $this->assertFalse($subject->isIncludedIn($lessSubject, TriState::No));
        $this->assertFalse($subject->isIncludedIn($lessSubject, TriState::Maybe));
        $this->assertFalse($subject->isIncludedIn($lessSubject, TriState::Yes));

        assert($lessSubject instanceof Bag || $lessSubject instanceof Set);
        $this->assertFalse($lessSubject->isIncludedIn($subject, TriState::No));
        $this->assertTrue($lessSubject->isIncludedIn($subject, TriState::Maybe));
        $this->assertTrue($lessSubject->isIncludedIn($subject, TriState::Yes));
    }
}
