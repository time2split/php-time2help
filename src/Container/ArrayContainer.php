<?php

declare(strict_types=1);

namespace Time2Split\Help\Container;

use Time2Split\Help\Container\Class\ElementsUpdating;
use Time2Split\Help\Container\Class\FetchingClosed;
use Time2Split\Help\Container\Class\IsUnmodifiable;
use Time2Split\Help\Container\Class\ToArray;

/**
 * A container working like a php array.
 *
 * A method call to an existing php {@link https://www.php.net/manual/en/ref.array.php array_*}
 * or a {@see Time2Split\Help\Arrays} function calls this fonction on the internal storage and
 * returns this ArrayContainerInstance with the resulting array as internal storage.
 * If the result type declared by the function is not an array it is returned directly.
 * 
 * ```
 * $ac = ArrayContainers::create(['A','B']);
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
 * @package time2help\container\php
 * 
 * 
 * @template K
 * @template V
 * 
 * @extends ContainerAA<K,V>
 * @extends ToArray<K,V>
 * @extends ElementsUpdating<V>
 */
interface ArrayContainer
extends
    ContainerAA,
    ToArray,
    ElementsUpdating
{
    /**
     * @param array<K,V> $arguments
     */
    public function __call(string $name, array $arguments): mixed;

    /**
     * @return IsUnmodifiable&ArrayContainer<K,V>
     */
    #[\Override]
    public function unmodifiable(): ArrayContainer&IsUnmodifiable;
}
