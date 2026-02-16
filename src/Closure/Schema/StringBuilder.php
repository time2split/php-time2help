<?php

declare(strict_types=1);

namespace Time2Split\Help\Closure\Schema;

class StringBuilder extends SchemaBuilder
{
    public final function strIs(string $string): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => (string) $funValue === $string,
        );
    }

    public final function strStartsWith(string $prefix): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => \str_starts_with((string)$funValue, $prefix),
        );
    }

    public final function strEndsWith(string $suffix): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => \str_ends_with((string)$funValue, $suffix),
        );
    }

    public final function strContains(string $string): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => \str_contains((string)$funValue, $string),
        );
    }

    public final function preg_match(string $pattern): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => 1 === \preg_match($pattern, (string)$funValue),
        );
    }
}
