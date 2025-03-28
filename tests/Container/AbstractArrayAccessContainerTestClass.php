<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use Time2Split\Help\Classes\GetUnmodifiable;
use Time2Split\Help\Container\ArrayAccessUpdating;
use Time2Split\Help\Container\Clearable;
use Time2Split\Help\Container\ContainerPutMethods;
use Time2Split\Help\Container\Entry;
use Time2Split\Help\Exception\UnmodifiableException;
use Time2Split\Help\Iterables;

/**
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractArrayAccessContainerTestClass extends AbstractContainerTestClass
{
    #[\Override]
    abstract protected static function provideContainer(): mixed;

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

    protected static final function provideContainerWithSubEntries(int $offset = 0, ?int $length = null)
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
        $subject[$k] = $v;
        $this->checkNotEmpty($subject, 1);
        $this->checkOffsetValue($subject, $k, $v);
        return [$subject, $k, $v];
    }

    #[Depends('testOffsetSetAAC')]
    final public function testOffsetUnsetAAC(array $in): void
    {
        [$subject, $k, $v] =  $in;
        unset($subject[$k]);
        $this->checkEmpty($subject);
    }

    // ========================================================================

    final public function testPutMoreAAC()
    {
        $subject = static::provideContainer();

        $a = fn() => Entry::traverseListOfEntries(static::provideSubEntries(0, 3));
        $b = fn() => Entry::traverseListOfEntries(static::provideSubEntries(3, 3));
        $array = fn() => Iterables::append($a(), $b());

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

    #[Depends('testPutMoreAAC')]
    final public function testDropMoreFromAAC($subject): void
    {
        $a = static::provideSubEntries(0, 2);
        $b = static::provideSubEntries(1, 3);

        $a = Entry::traverseListOfEntries($a);
        $b = Entry::traverseListOfEntries($b);

        $a = Iterables::keys($a);
        $b = Iterables::keys($b);

        $a = \iterator_to_array($a);
        $b = \iterator_to_array($b);

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
                ContainerPutMethods::class,
                function ($subject) {
                    $subject->putMore();
                }
            ],
            'putFromList' =>
            [
                ContainerPutMethods::class,
                function ($subject) {
                    $subject->putFromList();
                }
            ],
        ];
    }

    #[DataProvider('provideUnmodifiableCallables')]
    final public function testUnmodifiableAAC(string $requiredClass, callable $modify): void
    {
        $subject = static::provideContainer();

        if (!($subject instanceof GetUnmodifiable) || !($subject instanceof $requiredClass))
            $this->markTestSkipped();

        $unmodif = $subject->unmodifiable();
        $this->expectException(UnmodifiableException::class);
        $modify($unmodif);
    }
}
