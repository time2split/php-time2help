<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Reflection;

use ReflectionParameter;
use Time2Split\Help\Schema\Operator\AndSchema;
use Time2Split\Help\Schema\Scalar\StringSchema;

/**
 * @package time2help\schema\reflection
 */
final class ParameterSchema
extends AndSchema
{

    public function validateElement($element): bool
    {
        if (!$element instanceof ReflectionParameter)
            return false;

        return parent::validateElement($element);
    }

    // ========================================================================
    // BUILDER

    public function type(): TypeSchema
    {
        return $this->buildSchema(new TypeSchema(fn(ReflectionParameter $param) => $param->getType()));
    }

    public function name(): StringSchema
    {
        return $this->buildSchema(new StringSchema(fn(ReflectionParameter $param) => $param->getName()));
    }

    // ========================================================================
    // BOOL

    public final function allowsNull(bool $yes = true): static
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionParameter $param) => $param->allowsNull() === true
                : fn(ReflectionParameter $param) => $param->allowsNull() === false

        );
        return $this;
    }

    public final function hasType(bool $yes = true): static
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionParameter $param) => $param->hasType() === true
                : fn(ReflectionParameter $param) => $param->hasType() === false
        );
        return $this;
    }

    public final function isOptional(bool $yes = true): static
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionParameter $param) => $param->isOptional() === true
                : fn(ReflectionParameter $param) => $param->isOptional() === false
        );
        return $this;
    }

    public final function isVariadic(bool $yes = true): static
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionParameter $param) => $param->isVariadic() === true
                : fn(ReflectionParameter $param) => $param->isVariadic() === false
        );
        return $this;
    }

    public final function isPromoted(bool $yes = true): static
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionParameter $param) => $param->isPromoted() === true
                : fn(ReflectionParameter $param) => $param->isPromoted() === false
        );
        return $this;
    }

    public final function isPassedByReference(bool $yes = true): static
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionParameter $param) => $param->isPassedByReference() === true
                : fn(ReflectionParameter $param) => $param->isPassedByReference() === false
        );
        return $this;
    }

    public final function canBePassedByValue(bool $yes = true): static
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionParameter $param) => $param->canBePassedByValue() === true
                : fn(ReflectionParameter $param) => $param->canBePassedByValue() === false
        );
        return $this;
    }

    public final function isDefaultValueAvailable(bool $yes = true): static
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionParameter $param) => $param->isDefaultValueAvailable() === true
                : fn(ReflectionParameter $param) => $param->isDefaultValueAvailable() === false
        );
        return $this;
    }

    public final function isDefaultValueConstant(bool $yes = true): static
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionParameter $param) => ($param->isDefaultValueAvailable() && $param->isDefaultValueConstant()) === true
                : fn(ReflectionParameter $param) => ($param->isDefaultValueAvailable() && $param->isDefaultValueConstant()) === false
        );
        return $this;
    }
}
