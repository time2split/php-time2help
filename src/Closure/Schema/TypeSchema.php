<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema;

use ReflectionType;
use Time2Split\Help\Closure\Schema\Impl\AbstractSchemaOfSchema;

final class TypeSchema
extends AbstractSchemaOfSchema
{
    public final function validate($element): bool
    {
        return $this->_validate($element);
    }

    public final function _validate(ReflectionType $type): bool
    {
        return parent::validate($type);
    }

    // ========================================================================
    // BOOL

    public final function allowsNull(bool $yes = true): self
    {
        $this->buildSchemaFromClosure(
            $yes
                ? fn(ReflectionType $param) => $param->allowsNull() === true
                : fn(ReflectionType $param) => $param->allowsNull() === false
        );
        return $this;
    }

    // ========================================================================
    // BUILDER

    public function name(): StringBuilder
    {
        return $this->setBuilder(new StringBuilder($this, fn(ReflectionType $type) => (string)$type));
    }
}
