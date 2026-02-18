<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Reflection;

use Closure;
use ReflectionFunction;
use Time2Split\Help\Schema\Impl\AbstractSchemaOfSchema;
use Time2Split\Help\Schema\IntSchema;
use Time2Split\Help\Schema\StringSchema;

/**
 * Validates a closure.
 *  
 * @package time2help\schema\reflection
 */
final class ClosureSchema
extends AbstractSchemaOfSchema
{
    public function validateElement($element): bool
    {
        if ($element instanceof ReflectionFunction);
        elseif ($element instanceof Closure || \is_callable($element))
            $element = new ReflectionFunction($element);
        else
            throw new \InvalidArgumentException("$element must be a closure/callable");

        return parent::validateElement($element);
    }

    // ========================================================================
    // BOOL

    public function hasReturnType(bool $yes = true): self
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionFunction $function) => $function->hasReturnType()
                : fn(ReflectionFunction $function) => !$function->hasReturnType()
        );
        return $this;
    }

    // ========================================================================
    // SCHEMA

    /**
     * Gets a parameter schema.
     * 
     * @param int $pos
     *      The position of the parameter in the list of parameters.
     * @return ParameterSchema
     *      The schema.
     */
    public function parameterAt(int $pos): ParameterSchema
    {
        return $this->buildSchema(
            new ParameterSchema($this, fn(ReflectionFunction $fn) => $fn->getParameters()[$pos])
        );
    }

    /**
     * Validates the first parameters of a parameter list.
     * 
     * @param ParameterSchema $firstParameter
     *      The schema for the first parameter.
     * @param ParameterSchema ...$nextParameters
     *      The schema for the next parameters.
     * @return static
     *      This schema.
     */
    public function parameters(ParameterSchema $firstParameter, ParameterSchema ...$nextParameters): static
    {
        $schemas = [$firstParameter, ...$nextParameters];

        foreach ($schemas as &$s) {
            $s = $s->commit();
        }
        $this->buildSchemaFromClosure(

            function (ReflectionFunction $fn) use ($schemas) {
                $nbSchemas = \count($schemas);
                $funparams = $fn->getParameters();

                if (\count($funparams) < $nbSchemas)
                    return false;

                for ($i = 0; $i < $nbSchemas; $i++) {
                    $schema = $schemas[$i];
                    $fparam = $funparams[$i];

                    if (!$schema->validate($fparam))
                        return false;
                }
                return true;
            }
        );
        return $this;
    }

    /**
     * Gets a string schema on the closure short name.
     * 
     * @return StringSchema
     *      The schema.
     */
    public function shortName(): StringSchema
    {
        return $this->buildSchema(new StringSchema($this, fn(ReflectionFunction $fn) => $fn->getShortName()));
    }

    /**
     * Gets a string schema on the closure namespace.
     * 
     * @return StringSchema
     *      The schema.
     */
    public function namespace(): StringSchema
    {
        return $this->buildSchema(new StringSchema($this, fn(ReflectionFunction $fn) => $fn->getNamespaceName()));
    }

    /**
     * Gets a string schema on the closure name ($shortName\$namespace).
     * 
     * @return StringSchema
     *      The schema.
     */
    public function name(): StringSchema
    {
        return $this->buildSchema(new StringSchema($this, fn(ReflectionFunction $fn) => $fn->getName()));
    }

    /**
     * Gets a type schema on the closure return type.
     * 
     * @return TypeSchema
     *      The schema.
     */
    public function returnType(): TypeSchema
    {
        return $this->buildSchema(new TypeSchema($this, fn(ReflectionFunction $fn) => $fn->getReturnType()));
    }

    /**
     * Gets an integer schema on the closure's number of paramters.
     * 
     * @return IntSchema
     *      The schema.
     */
    public function numberOfParameters(): IntSchema
    {
        return $this->buildSchema(new IntSchema($this, fn(ReflectionFunction $fn) => $fn->getNumberOfParameters()));
    }

    /**
     * Gets an integer schema on the closures's number of required parameters.
     * 
     * @return IntSchema
     *      The schema.
     */
    public function numberOfRequiredParameters(): IntSchema
    {
        return $this->buildSchema(new IntSchema($this, fn(ReflectionFunction $fn) => $fn->getNumberOfRequiredParameters()));
    }
}
