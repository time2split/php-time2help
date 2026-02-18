<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Impl;

use Closure;
use Time2Split\Help\Closure\Closures;
use Time2Split\Help\Functions;
use Time2Split\Help\Schema\Class\IsUnmodifiable;
use Time2Split\Help\Schema\IntSchema;
use Time2Split\Help\Schema\ObjectSchema;
use Time2Split\Help\Schema\OfSchemas;
use Time2Split\Help\Schema\Schema;
use Time2Split\Help\Schema\Schemas;
use Time2Split\Help\Schema\StringSchema;

/**
 * Implementation for a schema of child schemas.
 * 
 * @package time2help\schema
 */
abstract class AbstractSchemaOfSchema
extends AbstractSchema
implements
    OfSchemas
{
    /**
     * The list of child schemas.
     * 
     * @var list<Schema>
     */
    protected array $schemas = [];

    private Schema&OfSchemas $lastSchemaOfSchemas;

    /**
     * @var Closure(mixed):mixed $transformElement
     */
    private readonly Closure $transformElement;

    /**
     * @param null|(Schema&OfSchemas) $parent
     *      The parent schema of this schema.
     * @param ?Closure(mixed):mixed $transformElement
     *      Transforms the element to be validate.
     *       - `$transformElement(mixed $element):mixed`
     * 
     *      This function is called in the method {@see \Time2Split\Help\Schema\Impl\AbstractSchemaOfSchema::validate()}, then
     *      the result is passed as the argument of {@see \Time2Split\Help\Schema\Impl\AbstractSchemaOfSchema::validateElement()}.

     */
    public function __construct(
        null|(Schema&OfSchemas) $parent = null,
        ?Closure $transformElement = null
    ) {
        parent::__construct($parent);
        $this->transformElement = $transformElement ?? Functions::identity(...);
        $this->lastSchemaOfSchemas = $this;
    }

    public function __clone(): void
    {
        $this->forgetParent();

        foreach ($this->schemas as $k => $v) {

            if ($v instanceof self)
                $this->schemas[$k] = clone $this->schemas[$k];
        };
    }

    // ========================================================================

    private function addSchema(Schema $schema): void
    {
        $this->schemas[] = $schema;
    }

    // ========================================================================

    /**
     * Add a schema to the list of child schemas.
     * 
     * @param Schema $schema
     *      The child schema to add.
     * @template S of Schema
     * 
     * @phpstan-param S $schema
     * @phpstan-return S
     */
    protected final function buildSchema(
        Schema $schema,
    ): Schema {
        $this->addSchema($schema);

        if ($schema instanceof OfSchemas)
            $this->lastSchemaOfSchemas = $schema;
        else
            $this->lastSchemaOfSchemas = $this;

        return $schema;
    }

    /**
     * Creates a new unmodifiable schema from a validation closure
     * and add it to list of child schemas.
     * 
     * @param Closure(mixed):bool $validate
     *      Validate an element.
     *       - `$validate(mixed $element):bool`
     * 
     * @return Schema&OfSchemas
     *      The parent schema of `$this` schema,
     *      or `$this` if there is no parent.
     */
    protected final function buildSchemaFromClosure(
        Closure $validate,
    ): Schema&OfSchemas {
        $this->buildSchema(Schemas::fromClosure($validate));
        return $this->parent ?? $this;
    }

    // ========================================================================

    #[\Override]
    public final function schema(Schema $schema): static
    {
        if (
            \get_class($schema) === static::class &&
            $this->transformElement == $this->transformElement
        ) {
            // Copy the childs if the two schemas does the same thing
            $this->schemas = \array_merge($this->schemas, $schema->schemas);
        } else {

            if (
                $schema instanceof OfSchemas &&
                !$schema instanceof IsUnmodifiable
            )
                $schema = $schema->commit();

            $this->buildSchema($schema);
        }
        $this->lastSchemaOfSchemas = $this;
        return $this;
    }

    #[\Override]
    public final function and(): Schema&OfSchemas
    {
        return $this->lastSchemaOfSchemas;
    }

    #[\Override]
    public final function up(): Schema&OfSchemas
    {
        return $this->parent ?? $this;
    }

    #[\Override]
    public final function commit(): Schema&IsUnmodifiable
    {
        if ($this instanceof IsUnmodifiable)
            return $this;
        if (null !== $this->parent)
            return $this->parent->commit();

        return Schemas::fromClosure((clone $this)->validate(...));
    }

    // ========================================================================

    #[\Override]
    public final function int(bool $castToInt = false): IntSchema
    {
        return $this->buildSchema(new IntSchema($this, $castToInt ? Functions::castToInt(...) : null));
    }

    #[\Override]
    public final function string(bool $castToString = false): StringSchema
    {
        return $this->buildSchema(new StringSchema($this, $castToString ? Functions::castToString(...) : null));
    }

    #[\Override]
    public final function object(bool $castToObject = false): ObjectSchema
    {
        return $this->buildSchema(new ObjectSchema($this, $castToObject ? Functions::castToObject(...) : null));
    }

    // ========================================================================

    #[\Override]
    public final function isset(bool $isset = true): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            $isset
                ? fn(mixed $funValue) => $funValue !== null
                : fn(mixed $funValue) => $funValue === null
        );
    }

    #[\Override]
    public final function sameAs(mixed $value, mixed ...$orValue): Schema&OfSchemas
    {
        $fn = empty($orValue)
            ? fn(mixed $fvalue) => $fvalue === $value
            : Closures::anyPredicate([$value, ...$orValue], Functions::areTheSame(...));

        return $this->buildSchemaFromClosure($fn);
    }

    #[\Override]
    public final function isOfType(string $type, string ...$orType): Schema&OfSchemas
    {
        $fn = empty($orType)
            ? fn(mixed $value) => $type === \gettype($value)
            : Closures::anyPredicate([$type, ...$orType], fn($a, $b) => $a === \gettype($b));

        return $this->buildSchemaFromClosure($fn);
    }

    /**
     * @internal
     */
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

    // ========================================================================

    /**
     * Validate the element according the internal child schemas.
     * 
     * It cannot be overidden,
     * {@see AbstractSchemaOfSchema::validateElement()}
     * is defined to be overidden.
     * 
     * @param mixed $element
     *      The element to validate.
     * @return bool
     *      Returns `true` if each child schema validate the element,
     *      otherwise `false`.
     */
    #[\Override]
    public final function validate(mixed $element): bool
    {
        $element = ($this->transformElement)($element);
        return $this->validateElement($element);
    }

    /**
     * Validate the transformed element.
     * 
     * It must be override to implements new validation behaviours.
     * 
     * @param mixed $element
     *      The transformed element to validate.
     * @return bool
     *      Returns `true` if each child schema validate the element,
     *      otherwise `false`.
     */
    public function validateElement(mixed $element): bool
    {
        foreach ($this->schemas as $schema) {

            if (!$schema->validate($element))
                return false;
        }
        return true;
    }
}
