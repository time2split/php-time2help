<?php

declare(strict_types=1);

namespace Time2Split\Help\Tests\Container\Bag;

use Time2Split\Help\Tests\Container\AbstractBagSetTestClass;

/**
 * @author Olivier Rodriguez (zuri)
 */
abstract class AbstractBagTestClass extends AbstractBagSetTestClass
{
    protected static function provideEntries(): array
    {
        return [
            ['a' => 1],
            ['c' => 1],
            ['d' => 1],
            ['e' => 1],
            ['f' => 1],
            ['g' => 1],
        ];
    }
    #[\Override]
    protected static function arrayValueIsAbsent(mixed $value): bool
    {
        return $value === 0;
    }
    #[\Override]
    protected static function arrayValueIsPresent(mixed $value): bool
    {
        return $value > 0;
    }

    // ========================================================================

    public final function testBagUpdate(): void
    {
        $subject = $this->provideContainer();
        $entries = $this->provideEntryObjects();

        // Insert
        $subject[($e = $entries[0])->key] = 1;
        $this->checkNotEmpty($subject, 1);
        $this->checkOffsetValue($subject, $e->key, 1);

        $subject[($e = $entries[1])->key] = 2;
        $this->checkNotEmpty($subject, 3);
        $this->checkOffsetValue($subject, $e->key, 2);

        // No op
        $subject[($e = $entries[0])->key] = 0;
        $this->checkNotEmpty($subject, 3);
        $this->checkOffsetValue($subject, $e->key, 1);
        $subject[$entries[2]->key] = false;
        $this->checkNotEmpty($subject, 3);
        $subject[$entries[2]->key] = -1;
        $this->checkNotEmpty($subject, 3);

        // Drop
        $subject[($e = $entries[1])->key] = false;
        $this->checkNotEmpty($subject, 2);
        $this->checkOffsetValue($subject, $e->key, 1);
        $subject[($e = $entries[1])->key] = -1;
        $this->checkNotEmpty($subject, 1);
        $this->checkOffsetNotExists($subject, $e->key);

        $subject[($e = $entries[0])->key] = -99;
        $this->checkEmpty($subject);
    }

    public final function testBagIteration(): void
    {
        $subject = $this->provideContainer();
        $entries = $this->provideEntryObjects();

        $subject[$entries[0]->key] = 1;
        $subject[$entries[1]->key] = 3;
        $subject[$entries[2]->key] = 1;

        $expect = function () use ($entries) {
            yield $entries[0]->key => 1;
            yield $entries[1]->key => 3;
            yield $entries[2]->key => 1;
        };
        foreach ($expect() as $k => $v)
            $this->checkOffsetValue($subject, $k, $v);
    }
}
