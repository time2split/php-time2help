<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use PHPUnit\Framework\Attributes\DataProvider;
use Time2Split\Help\Container\Class\ArrayAccessUpdating;
use Time2Split\Help\Container\Class\Clearable;
use Time2Split\Help\Container\Class\ElementsUpdating;
use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\ContainerAA;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Exception\UnmodifiableException;
use Time2Split\Help\Iterables;

/**
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractArrayAccessContainerTestClass extends AbstractContainerTestClass
{
    use ContainerAATestUtils;

    #[\Override]
    abstract protected static function provideContainer(): ContainerAA;

    /**
     * @return array of pairs [key,value]
     */
    protected static function provideEntries(): array
    {
        return [
            ['a' => 0],
            ['c' => 1],
            ['d' => 2],
            ['e' => 3],
            ['f' => 4],
            ['g' => 6],
        ];
    }

    protected static function provideContainerWithSubEntries(int $offset = 0, ?int $length = null): ContainerAA
    {
        $subject = static::provideContainer();

        foreach (
            Entry::traverseListOfEntries(
                static::provideSubEntries($offset, $length)
            ) as $k => $v
        )
            $subject[$k] = $v;

        return $subject;
    }


    // ========================================================================
    // Test One Entry

    final public function testOffsetSetAAC(): array
    {
        $subject = static::provideContainer();
        $entry = static::provideEntryObjects()[0];
        [$k, $v] = [$entry->key, $entry->value];

        if ($subject instanceof IsUnmodifiable)
            $this->expectException(UnmodifiableException::class);

        $subject[$k] = $v;
        $this->checkNotEmpty($subject, 1);
        $this->checkOffsetValue($subject, $k, $v);
        return [$subject, $k, $v];
    }

    final public function testOffsetGetAAC(): void
    {
        $subject = static::provideContainerWithSubEntries(0, 1);
        $entry = static::provideEntryObjects()[0];
        [$k, $v] = [$entry->key, $entry->value];
        $this->checkOffsetValue($subject, $k, $v);
    }

    final public function testOffsetExistsAAC(): void
    {
        $subject = static::provideContainerWithSubEntries(0, 1);
        $entry = static::provideEntryObjects()[0];
        [$k, $v] = [$entry->key, $entry->value];
        $this->checkOffsetExists($subject, $k);
        $entry = static::provideEntryObjects()[1];
        $this->checkOffsetNotExists($subject, $entry->key);
    }

    final public function testOffsetUnsetAAC(): void
    {
        $subject = static::provideContainerWithSubEntries(0, 1);
        $entry = static::provideEntryObjects()[0];
        [$k, $v] = [$entry->key, $entry->value];

        if ($subject instanceof IsUnmodifiable)
            $this->expectException(UnmodifiableException::class);

        unset($subject[$k]);
        $this->checkEmpty($subject);
    }

    // ========================================================================

    final public function testUpdateEntriesAAC()
    {
        $subject = static::provideContainer();

        $a = fn() => Entry::traverseListOfEntries(static::provideSubEntries(0, 3));
        $b = fn() => Entry::traverseListOfEntries(static::provideSubEntries(3, 3));
        $array = fn() => Iterables::append($a(), $b());

        if ($subject instanceof IsUnmodifiable)
            $this->expectException(UnmodifiableException::class);

        $subject
            ->updateEntries($a())
            ->updateEntries($b())
        ;
        $this->checkNotEmpty($subject, 6);
        $this->checkEqualsProvidedEntries($subject, $array());

        foreach ($array() as $k => $v) {
            $this->checkOffsetValue($subject, $k, $v);
        }
        return $subject;
    }

    final public function testUnsetMoreAAC(): void
    {
        $subject = $this->provideContainerWithSubEntries();
        $a = static::provideSubEntries(0, 2);
        $b = static::provideSubEntries(1, 3);

        $a = Entry::traverseListOfEntries($a);
        $b = Entry::traverseListOfEntries($b);

        $a = Iterables::keys($a);
        $b = Iterables::keys($b);

        $a = \iterator_to_array($a);
        $b = \iterator_to_array($b);

        if ($subject instanceof IsUnmodifiable)
            $this->expectException(UnmodifiableException::class);

        $subject->unsetMore(...$a);
        $this->checkNotEmpty($subject, 4);
        $subject->unsetFromList($b);
        $this->checkNotEmpty($subject, 2);
    }

    public static function provideUnmodifiableCallables(): iterable
    {
        return [
            'offsetSet' =>
            [
                \ArrayAccess::class,
                function ($subject) {
                    $subject->offsetSet('k', 'v');
                }
            ],
            'offsetUnset' =>
            [
                \ArrayAccess::class,
                function ($subject) {
                    $subject->offsetUnset('k', 'v');
                }
            ],
            'clear' =>
            [
                Clearable::class,
                function ($subject) {
                    $subject->clear();
                }
            ],
            'updateEntries' =>
            [
                ArrayAccessUpdating::class,
                function ($subject) {
                    $subject->updateEntries();
                }
            ],
            'unsetMore' =>
            [
                ArrayAccessUpdating::class,
                function ($subject) {
                    $subject->unsetMore();
                }
            ],
            'unsetFromList' =>
            [
                ArrayAccessUpdating::class,
                function ($subject) {
                    $subject->unsetFromList();
                }
            ],
            'putMore' =>
            [
                ElementsUpdating::class,
                function ($subject) {
                    $subject->putMore();
                }
            ],
            'putFromList' =>
            [
                ElementsUpdating::class,
                function ($subject) {
                    $subject->putFromList();
                }
            ],
        ];
    }

    #[DataProvider('provideUnmodifiableCallables')]
    final public function testUnmodifiableExceptionAAC(string $requiredClass, callable $modify): void
    {
        $subject = static::provideContainer();

        if (!($subject instanceof $requiredClass))
            $this->markTestSkipped();

        $unmodif = $subject->unmodifiable();
        $this->expectException(UnmodifiableException::class);
        $modify($unmodif);
    }

    /**
     * Test that the modification of the initial subject alter the backed unmodifiable instance.
     */
    final public function testUnmodifiableAAC(): void
    {
        $subject = static::provideContainer();

        if ($subject instanceof IsUnmodifiable)
            $this->markTestSkipped();

        $entries = static::provideSubEntries();

        $unmodifiable = $subject->unmodifiable();
        $this->assertCount(0, $unmodifiable);

        $entry = $entries[0];
        $subject[$entry->key] = $entry->value;
        $this->assertCount(1, $unmodifiable);
        $this->checkOffsetValue($unmodifiable, $entry->key, $entry->value);

        $copy = $unmodifiable->copy();
        $this->assertInstanceOf(IsUnmodifiable::class, $copy);
        $this->assertCount(1, $copy);
        $this->checkOffsetValue($copy, $entry->key, $entry->value);
    }
}
