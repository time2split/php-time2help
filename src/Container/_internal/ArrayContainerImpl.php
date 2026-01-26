<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\_internal;

use Time2Split\Help\Closure\Captures;
use Time2Split\Help\Closure\Closures;
use Time2Split\Help\Closure\ParameterInjections;
use Time2Split\Help\Container\ArrayContainer;
use Time2Split\Help\Container\ArrayContainers;
use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\Trait\ArrayAccessPutValue;
use Time2Split\Help\Container\Trait\ArrayAccessUpdating;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\CountableWithStorage;
use Time2Split\Help\Container\Trait\IteratorAggregateWithStorage;
use Time2Split\Help\Container\Trait\IteratorToArray;
use Time2Split\Help\Container\Trait\ToArrayToArrayContainer;

/**
 * @author Olivier Rodriguez (zuri)
 * 
 * @template K
 * @template V
 * 
 * @implements ArrayContainer<K,V>
 * @implements \IteratorAggregate<K,V>
 */
abstract class ArrayContainerImpl
implements
    ArrayContainer,
    \IteratorAggregate
{
    /**
     * @use ArrayAccessPutValue<V>
     * @use ArrayAccessUpdating<K,V>
     * @use ArrayAccessWithStorage<K,V>
     * @use IteratorAggregateWithStorage<K,V>
     * @use IteratorToArray<K,V>
     * @use ToArrayToArrayContainer<K,V>
     */
    use
        ArrayAccessPutValue,
        ArrayAccessUpdating,
        ArrayAccessWithStorage,
        CountableWithStorage,
        IteratorAggregateWithStorage,
        IteratorToArray,
        ToArrayToArrayContainer;

    /**
     * @var array<K,V>|ArrayContainer<K,V> $storage
     */
    protected array|ArrayContainer $storage;

    /**
     * @param array<K,V>|ArrayContainer<K,V> $storage
     */
    public function __construct(array|ArrayContainer $storage = [])
    {
        $this->storage = $storage;
    }

    #[\Override]
    public function copy(): static
    {
        /** @var static<K,V> */
        return new static($this->storage);
    }

    /**
     * @return IsUnmodifiable&self<K,V>
     */
    #[\Override]
    public function unmodifiable(): ArrayContainer&IsUnmodifiable
    {
        return ArrayContainers::unmodifiable($this);
    }

    /*
    #[\Override]
    public static function null(): self
    {
        return ArrayContainers::null();
    }
    //*/

    #[\Override]
    public function clear(): void
    {
        $this->storage = [];
    }

    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        if (\is_array($this->storage))
            return \array_key_exists($offset, $this->storage);
        else
            return $this->storage->offsetExists($offset);
    }

    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->storage[$offset] ?? null;
    }

    // #[\Override]
    /*
    public function equals(
        ContainerBase $other,
        bool|callable $strictOrEquals = false
    ): bool {
        if ($this === $other)
            return true;
        if (!($other instanceof self))
            return false;
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
    //*/

    // #[\Override]
    /*
    public function isIncludedIn(
        ContainerBase $other,
        bool|callable $strictOrEquals = false,
        bool $strictInclusion = false,
    ): bool {
        if ($strictInclusion)
            return $this->isStrictlyIncludedIn($other);
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
                /* phpstan-ignore deadCode.unreachable * /
                found:
                unset($b[$kb]);
                return true;
            };
            $abdiff = Iterables::findEntriesWithoutRelation($relation, $a, $b);
        }
        $abdiff->rewind();
        return !$abdiff->valid();
    }
    //*/

    // ========================================================================

    #[\Override]
    public function __call(string $name, array $arguments): mixed
    {
        $name = \strtolower($name);

        if (\str_starts_with($name, 'array_')) {

            if (\is_callable($f = "\\$name"))
                $theFunction = $f(...);
        } elseif (\is_callable($f = ['\Time2Split\Help\Arrays', $name]))
            $theFunction = $f(...);

        if (!isset($theFunction))
            throw new \BadFunctionCallException("Function $name is not callable");

        /* @phpstan-ignore variable.undefined */
        $reflect = new \ReflectionFunction($theFunction);
        $return = $reflect->getReturnType();
        $call = Captures::inject(
            $theFunction,
            ParameterInjections::injectReference(
                Closures::or(
                    Captures::validParameterNameClosure('array'),
                    Captures::validParameterTypeClosure('array'),
                ),
                $this->storage
            ),
        );

        if ($call->isEmpty())
            throw new \BadFunctionCallException("Function $name is not callable with an array argument");

        $ret = $call->get()(...$arguments);

        if ((string)$return !== 'array')
            return $ret;

        $this->storage = $ret;
        return $this;
    }
}
