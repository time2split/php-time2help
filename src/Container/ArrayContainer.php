<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Arrays;
use Time2Split\Help\Container\Trait\ArrayAccessWithStorage;
use Time2Split\Help\Container\Trait\IteratorAggregateWithArrayStorage;

/**
 * A container working like a php array.
 *
 * A method call to an existing php `array_` function calls this fonction on
 * the internal storage and returns a new ArrayContainerInstance with the resulting
 * array as internal storage.
 * ```
 * $ac = new ArrayContainer(['A','B']);
 * $ac = $ac->array_merge(['a','b']);
 * print_r($ac->toArray());
 * // Display
 * // Array
 * // (
 * //     [0] => A
 * //     [1] => B
 * //     [2] => a
 * //     [3] => b
 * // )
 * ```
 * 
 * Replacing `array_` by `fluent_` works directly on the ArrayContainer instance and
 * returns this instance as a result.
 * ```
 * $ac->fluent_merge([1,2])->fluent_map(fn($i) => $i * 2);
 * print_r($ac->toArray());
 * // Display
 * // Array
 * // (
 * //     [0] => 2
 * //     [1] => 4
 * // )
 * ```
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
final class ArrayContainer
extends ContainerWithArrayStorage
implements \ArrayAccess
{
    use
        ArrayAccessWithStorage,
        IteratorAggregateWithArrayStorage;

    public function __construct(array ...$arrays)
    {
        if (1 === \count($arrays))
            parent::__construct(Arrays::firstValue($arrays));
        else
            parent::__construct(\array_merge(...$arrays));
    }

    // ========================================================================

    public function __call(string $name, array $arguments)
    {
        if (\str_starts_with($name, 'array_'))
            $fluent = false;
        elseif (\str_starts_with($name, 'fluent_')) {
            $fluent = true;
            $name = 'array' . \substr($name, 6);
        }
        $fun = "\\$name";

        if (!\function_exists($fun))
            throw new \BadFunctionCallException('Function ' . __CLASS__ . '::' . $name . ' is not callable');

        if ($name === 'array_map') {
            $callback = \array_pop($arguments);
            $array = \array_map($callback, $this->storage, ...$arguments);
        } else
            $array = $name($this->storage, ...$arguments);

        if ($fluent) {
            $this->storage = $array;
            return $this;
        } else
            return new ArrayContainer($array);
    }
}
