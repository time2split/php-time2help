<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure;

use Closure;

/**
 * Factories on ParameterInjection.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\closure
 * 
 * @see ParameterInjection
 */
final class ParameterInjections
{
    /**
     * Gets a parameter injection to inject a value.
     * 
     * @param Closure(\ReflectionParameter):bool $isValidParameter
     * A closure to select one parameter into the list of parameters of the overlying function.
     * @param mixed $value
     * A value to set as the parameter value.
     * @return ParameterInjection
     */
    public static function injectValue(
        Closure $isValidParameter,
        mixed $value = null
    ): ParameterInjection {
        return new class($isValidParameter, $value) extends ParameterInjection {

            public function __construct(
                Closure $isValidParameter,
                mixed $value
            ) {
                $this->isValidParameter = $isValidParameter;
                $this->value = $value;
            }

            #[\Override]
            public function capture(\ReflectionParameter $parameter): CapturedParameter
            {
                return new class($parameter, $this) extends CapturedParameter {

                    public function __construct(
                        \ReflectionParameter $parameter,
                        ParameterInjection $injection
                    ) {
                        $this->parameter = $parameter;
                        $this->value = $injection->value;
                    }
                };
            }
        };
    }

    /**
     * Gets a parameter injection to inject a reference.
     * 
     * @param Closure(\ReflectionParameter):bool $isValidParameter
     * A closure to select one parameter into the list of parameters of the overlying function.
     * @param mixed &$value
     * A reference to set as the parameter value.
     * @return ParameterInjection
     */
    public static function injectReference(
        Closure $isValidParameter,
        mixed &$value
    ): ParameterInjection {
        return new class($isValidParameter, $value) extends ParameterInjection {

            public function __construct(
                Closure $isValidParameter,
                mixed &$value
            ) {
                $this->isValidParameter = $isValidParameter;
                $this->value = &$value;
            }

            #[\Override]
            public function capture(\ReflectionParameter $parameter): CapturedParameter
            {
                return new class($parameter, $this) extends CapturedParameter {

                    public function __construct(
                        \ReflectionParameter $parameter,
                        ParameterInjection $injection
                    ) {
                        $this->parameter = $parameter;
                        $this->value = &$injection->value;
                    }
                };
            }
        };
    }
}
