<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema;

use Closure;

abstract class SchemaBuilder
{
    protected readonly ?Closure $getElements;

    protected readonly Closure $parentAddSchema;

    // ========================================================================

    public function __construct(
        protected readonly Schema&OfSchemas $schema,
        ?Closure $getElements = null,
    ) {
        $this->getElements = $getElements;
    }

    // ========================================================================

    // protected final function buildSchema(Schema $schema): Schema&OfSchemas
    // {
    //     $parent = $this->doBuild->parent;
    //     $parent->buildSchema($schema, $this);
    //     return $parent;
    // }

    // protected final function buildSchemaTransformElement(
    //     Schema $schema,
    //     Closure $transform,
    // ): Schema {
    //     $parent = $this->doBuild->parent;
    //     $parent->buildSchemaTransformElement($schema, $transform, $this);
    //     return $parent;
    // }

    protected final function buildSchemaFromClosure(Closure $check): Schema&OfSchemas
    {
        if (isset($this->getElements))
            $check = fn($element) => $check(($this->getElements)($element));

        $this->schema->buildSchemaFromClosure($check, $this);
        return $this->schema;
    }

    // ========================================================================

    public final function schema(Schema&OfSchemas $schema): Schema&OfSchemas {}

    public final function required(bool $required = true): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            $required
                ? fn(mixed $funValue) => $funValue !== null
                : fn(mixed $funValue) => $funValue === null
        );
    }

    public final function is(mixed $value): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => $funValue === $value,
        );
    }

    public final function isObject(bool $isObject = true): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            $isObject
                ? fn(mixed $funValue) => \is_object($funValue)
                : fn(mixed $funValue) => !\is_object($funValue)
        );
    }

    public final function instanceOf(string $class): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => \is_object($funValue) && \is_a($funValue, $class),
        );
    }

    public final function dump(?callable $dump = null): Schema&OfSchemas
    {
        $dump ??= error_dump(...);

        return $this->buildSchemaFromClosure(
            function (mixed $funValue) use ($dump) {
                $dump($funValue);
                $dump((string)$funValue);
                return true;
            }
        );
    }
}
