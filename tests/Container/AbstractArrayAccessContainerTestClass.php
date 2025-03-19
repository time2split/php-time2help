<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use Time2Split\Help\Classes\GetUnmodifiable;
use Time2Split\Help\Container\ArrayAccessContainer;
use Time2Split\Help\Container\ArrayAccessUpdateMethods;
use Time2Split\Help\Container\Clearable;
use Time2Split\Help\Container\Container;
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
    abstract protected static function provideContainer(): ArrayAccessContainer;

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

    protected static final function provideContainerWithSubEntries(int $offset = 0, ?int $length = null): ArrayAccessContainer
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

    final public function testPutMoreAAC(): ArrayAccessContainer
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
    final public function testDropMoreFromAAC(Container $subject): void
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
                ArrayAccessUpdateMethods::class,
                function ($subject) {
                    $subject->updateEntries();
                }
            ],
            'unsetMore' =>
            [
                ArrayAccessUpdateMethods::class,
                function ($subject) {
                    $subject->unsetMore();
                }
            ],
            'unsetFromList' =>
            [
                ArrayAccessUpdateMethods::class,
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


    // ========================================================================

    // public function testEquals(): void
    // {
    //     $a = static::provideContainer();
    //     $a->setFromList(static::provideThreeItems());
    //     $b = $a->copy();

    //     $this->assertTrue($this->containerEquals($a, $b));
    //     $absentItem = static::provideOneUnexistantItem();

    //     // Must be order independant
    //     $b = static::provideContainer();
    //     $b->setFromList(static::provideThreeItemsUnordered());
    //     $this->assertTrue($this->containerEquals($a, $b), 'Order dependency');
    //     $this->assertTrue($this->containerEquals($b, $a), 'Order dependency');

    //     $b[$absentItem] = true;
    //     $this->assertFalse($this->containerEquals($a, $b));
    // }

    // public function testIncludedIn()
    // {
    //     $items = static::provideThreeItems();
    //     $a = static::provideContainer();
    //     $a->setFromList($items);
    //     $b = $a->copy();

    //     $this->assertTrue($this->containerIncludedIn($a, $b), 'Not the sames');
    //     $this->assertTrue($this->containerIncludedIn($b, $a), 'Not the sames');

    //     // Must be order independant
    //     $b = static::provideContainer();
    //     $b->setFromList(static::provideThreeItemsUnordered());
    //     $this->assertTrue($this->containerIncludedIn($a, $b), 'Order dependency');
    //     $this->assertTrue($this->containerIncludedIn($b, $a), 'Order dependency');

    //     $items2 = $items;
    //     unset($items2[1]);
    //     $a = static::provideContainer();
    //     $a->setFromList($items2);
    //     $this->assertTrue($this->containerIncludedIn($a, $b), 'a < b');
    //     $this->assertFalse($this->containerIncludedIn($b, $a), 'b < a');

    //     $a->setMore(static::provideOneUnexistantItem());
    //     $this->assertFalse($this->containerIncludedIn($a, $b), 'a < b');
    //     $this->assertFalse($this->containerIncludedIn($b, $a), 'b < a');
    // }
}
