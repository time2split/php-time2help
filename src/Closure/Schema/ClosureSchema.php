<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema;

use Closure;
use ReflectionFunction;
use Time2Split\Help\Closure\Schema\Impl\AbstractSchemaOfSchema;

final class ClosureSchema
extends AbstractSchemaOfSchema
{
    public function validate($element): bool
    {
        if ($element instanceof ReflectionFunction);
        elseif ($element instanceof Closure || \is_callable($element))
            $element = new ReflectionFunction($element);
        else
            throw new \InvalidArgumentException("$element must be a closure/callable");

        return parent::validate($element);
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

    public function parameterAt(int $pos): ParameterSchema
    {
        return $this->buildSchemaTransformElement(
            new ParameterSchema($this),
            fn(ReflectionFunction $fn) => $fn->getParameters()[$pos]
        );
    }

    // ========================================================================
    // BUILDER

    public function shortName(): StringBuilder
    {
        return $this->setBuilder(new StringBuilder($this, fn(ReflectionFunction $fn) => $fn->getShortName()));
    }

    public function namespace(): StringBuilder
    {
        return $this->setBuilder(new StringBuilder($this, fn(ReflectionFunction $fn) => $fn->getNamespaceName()));
    }

    public function name(): StringBuilder
    {
        return $this->setBuilder(new StringBuilder($this, fn(ReflectionFunction $fn) => $fn->getName()));
    }

    public function returnType(): TypeBuilder
    {
        return $this->setBuilder(new TypeBuilder($this, fn(ReflectionFunction $fn) => $fn->getReturnType()));
    }

    public function numberOfParameters(): IntBuilder
    {
        return $this->setBuilder(new IntBuilder($this, fn(ReflectionFunction $fn) => $fn->getNumberOfParameters()));
    }

    public function numberOfRequiredParameters(): IntBuilder
    {
        return $this->setBuilder(new IntBuilder($this, fn(ReflectionFunction $fn) => $fn->getNumberOfRequiredParameters()));
    }
}
