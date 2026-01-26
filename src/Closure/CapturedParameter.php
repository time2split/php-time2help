<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure;

/**
 * A parameter selected into the list of arguments of a function.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\closure
 * 
 * @see ParameterInjection
 * @see Captures::captureParameters()
 */
abstract class CapturedParameter
{
    /**
     * @var \ReflectionParameter $parameter
     * The selected parameter.
     */
    public \ReflectionParameter $parameter;

    /**
     * @var mixed $value (`&$value`)
     * 
     * A reference to the value to inject as the parameter argument's value.
     * 
     * The reference will be used if the parameter is a reference
     * into the list of parameters of the function
     * (see {@see Captures::inject()}).
     * Otherwise a copy will be set.
     */
    public mixed $value;
}
