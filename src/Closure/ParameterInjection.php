<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure;

use Closure;

/**
 * Select a function parameter able to receive a value.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\closure
 * 
 * @see ParameterInjections
 * @see Captures::captureParameters()
 */
abstract class ParameterInjection
{
    /**
     * @var Closure(\ReflectionParameter):bool $isValidParameter
     * A closure to select one parameter into the list of parameters of the overlying function.
     */
    public Closure $isValidParameter;

    /**
     * @var mixed $value
     * A value to set as the (selected) parameter value when calling the overlying function.
     */
    public mixed $value = null;

    /**
     * @internal
     */
    public abstract function capture(\ReflectionParameter $parameter): CapturedParameter;
}
