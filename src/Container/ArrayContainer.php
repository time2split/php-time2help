<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Trait\ArrayAccessUpdating;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\IteratorAggregateWithArrayStorage;
use Time2Split\Help\Iterables;

/**
 * A container working like a php array.
 *
 * A method call to an existing php {@link https://www.php.net/manual/en/ref.array.php array_*}
 * or a {@see Time2Split\Help\Arrays} function calls this fonction on the internal storage and
 * returns this ArrayContainerInstance with the resulting array as internal storage.
 * If the result type declared by the function is not an array it is returned directly.
 * 
 * ```
 * $ac = new ArrayContainer(['A','B']);
 * $ac = $ac->array_merge(['a','b'])->array_reverse();
 * print_r($ac->toArray());
 * echo "first:", $ac->firstValue();
 * // Display
 * // Array
 * // (
 * //     [0] => b
 * //     [1] => a
 * //     [2] => B
 * //     [3] => A
 * // )
 * // first:b
 * ```
 * 
 * @see https://www.php.net/manual/en/ref.array.php
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
abstract class ArrayContainer
extends ContainerWithArrayStorage
implements
    ArrayAccessContainer,
    FetchingOpened
{
    use
        ArrayAccessUpdating,
        ArrayAccessWithStorage,
        Trait\FetchingOpened,
        IteratorAggregateWithArrayStorage;

    #[\Override]
    public function unmodifiable(): self
    {
        return ArrayContainers::Unmodifiable($this);
    }

    #[\Override]
    public static function null(): self
    {
        return ArrayContainers::null();
    }

    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        return \array_key_exists($offset, $this->storage);
    }

    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->storage[$offset] ?? null;
    }

    #[\Override]
    public function equals(
        ArrayContainer $other,
        bool|callable $strictOrEquals = false
    ): bool {
        if ($this === $other)
            return true;
        if (true === $strictOrEquals)
            return $this->storage === $other->storage;

        $ca = $this->count();
        $cb = $other->count();

        if ($ca !== $cb)
            return false;
        if (false === $strictOrEquals)
            return $this->storage == $other->storage;

        $ab = \array_map(null, $this->storage, $other->storage);
        return Iterables::walksUntil($ab, $strictOrEquals);
    }

    #[\Override]
    public function isIncludedIn(
        ArrayContainer $other,
        bool|callable $strictOrEquals = false,
        bool $strictInclusion = false,
    ): bool {
        if ($strictInclusion)
            return $this->isStrictlyIncludedIn($other, $strictOrEquals);
        if ($this === $other)
            return true;

        $ca = $this->count();
        $cb = $other->count();

        if ($ca > $cb)
            return false;

        $a = $this->storage;
        $b = $this->storage;

        if (true === $strictOrEquals) {
            $abdiff = \array_diff($a, $b);
            return empty($abdiff);
        }
        if (false === $strictOrEquals) {
            $abdiff = Iterables::valuesInjectionDiff($a, $b, false);
        } else {
            $relation = function ($ka, $va, &$b) use ($strictOrEquals): bool {

                foreach ($b as $kb => $vb) {
                    if ($strictOrEquals($va, $vb))
                        goto found;
                }
                return false;
                found:
                unset($b[$kb]);
                return true;
            };
            $abdiff = Iterables::findEntriesWithoutRelation($relation, $a, $b);
        }
        $abdiff->rewind();
        return !$abdiff->valid();
    }

    // ========================================================================

    public function __call(string $name, array $arguments)
    {
        $name = \strtolower($name);

        if (\str_starts_with($name, 'array_')) {

            if (\is_callable($f = "\\$name"))
                $theFunction = $f(...);
        } elseif (\is_callable($f = ['\Time2Split\Help\Arrays', $name]))
            $theFunction = $f(...);

        if (!isset($theFunction))
            goto error;

        /* @phpstan-ignore variable.undefined */
        $reflect = new \ReflectionFunction($theFunction);
        $return = $reflect->getReturnType();

        $params = $reflect->getParameters();
        $preArguments = [];

        foreach ($params as $p) {
            $type = $p->getType();

            if ((string)$type === 'array') {
                $isRef = $p->isPassedByReference();
                break;
            }
            $preArguments[] = array_shift($arguments);
        }

        /* @phpstan-ignore variable.undefined */
        if ($isRef)
            /* @phpstan-ignore variable.undefined */
            $ret = $theFunction(...[...$preArguments, &$this->storage, ...$arguments]);
        else
            /* @phpstan-ignore variable.undefined */
            $ret = $theFunction(...[...$preArguments, $this->storage, ...$arguments]);

        if ((string)$return !== 'array')
            return $ret;

        $this->storage = $ret;
        return $this;
        error:
        throw new \BadFunctionCallException('Function ' . __CLASS__ . '::' . $name . ' is not callable');
    }
}
