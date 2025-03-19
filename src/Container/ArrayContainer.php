<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Trait\ArrayAccessUpdateMethods;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\IteratorAggregateWithArrayStorage;

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
implements ArrayAccessContainer
{
    use
        ArrayAccessUpdateMethods,
        ArrayAccessWithStorage,
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

        if ($isRef)
            $ret = $theFunction(...[...$preArguments, &$this->storage, ...$arguments]);
        else
            $ret = $theFunction(...[...$preArguments, $this->storage, ...$arguments]);

        if ((string)$return !== 'array')
            return $ret;

        $this->storage = $ret;
        return $this;
        error:
        throw new \BadFunctionCallException('Function ' . __CLASS__ . '::' . $name . ' is not callable');
    }
}
