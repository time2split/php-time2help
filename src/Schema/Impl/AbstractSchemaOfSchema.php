<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Impl;

use Closure;
use Time2Split\Help\Arrays;
use Time2Split\Help\Closure\Closures;
use Time2Split\Help\Container\Trait\ContainerWithArrayStorage;
use Time2Split\Help\Functions;
use Time2Split\Help\Schema\Class\IsUnmodifiable;
use Time2Split\Help\Schema\OfSchemas;
use Time2Split\Help\Schema\Scalar\IntSchema;
use Time2Split\Help\Schema\Scalar\ObjectSchema;
use Time2Split\Help\Schema\Scalar\StringSchema;
use Time2Split\Help\Schema\Schema;
use Time2Split\Help\Schema\Schemas;

/**
 * Implementation for a schema of child schemas.
 * It validates the intersection of its childs.
 * 
 * @package time2help\schema
 * 
 * @implements \IteratorAggregate<int,Schema>
 */
abstract class AbstractSchemaOfSchema
extends AbstractSchema
implements
    OfSchemas,
    \IteratorAggregate
{
    /**
     * @use ContainerWithArrayStorage<int,Schema>
     */
    use
        ContainerWithArrayStorage;

    /**
     * The list of child schemas.
     * 
     * @var list<Schema>
     */
    private array $storage = [];

    /**
     * @var Closure(mixed):mixed $transformElement
     */
    private readonly Closure $transformElement;

    // ========================================================================

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
    public abstract function validateElement(mixed $element): bool;

    // ========================================================================

    /**
     * @param ?Closure(mixed):mixed $transformElement
     *      Transforms the element to be validate.
     *       - `$transformElement(mixed $element):mixed`
     * 
     *      This function is called in the method {@see \Time2Split\Help\Schema\Impl\AbstractSchemaOfSchema::validate()}, then
     *      the result is passed as the argument of {@see \Time2Split\Help\Schema\Impl\AbstractSchemaOfSchema::validateElement()}.
     * @param Schema[] $childs
     */
    public function __construct(
        ?Closure $transformElement = null,
        array $childs = []
    ) {
        $this->transformElement = $transformElement ?? Functions::identity(...);
        $this->addThenCommitSchemas(...$childs);
    }

    // ========================================================================

    public function copy(): static
    {
        $copy = clone $this;

        foreach ($copy as $k => $schema) {

            if ($schema instanceof OfSchemas) {
                $copy->storage[$k] = $schema->copy();
            } elseif (!$schema instanceof IsUnmodifiable) {
                $type = \get_class($schema);
                throw new \InvalidArgumentException("Unable to copy a schema of type $type");
            }
        }
        return $copy;
    }

    public function unmodifiable(): Schema&IsUnmodifiable
    {
        if ($this instanceof IsUnmodifiable)
            return $this;

        return Schemas::fromClosure($this->validate(...));
    }

    // ========================================================================

    #[\Override]
    protected function commitThis(): static
    {
        if ($this->committed())
            return $this;

        $ret = parent::commitThis();
        $ret->commitLastChildSchema();
        $ret->parent = null;

        foreach ($ret as $schema) {

            if ($schema instanceof AbstractSchema && !$schema->committed())
                throw new \AssertionError("A child schema have not been commited");
        }
        return $ret;
    }

    private function commitLastChildSchema(): void
    {
        $pos = Arrays::lastKey($this->storage);

        if (null === $pos)
            return;

        $this->commitStoredSchema($pos);
    }

    private function commitStoredSchema(int $pos): void
    {
        $schema = $this->storage[$pos];

        if ($schema instanceof AbstractSchema) {
            /*
                $last may have been commited if it was added
                from inside a constructor `childs` argument
            */
            if (!$schema->committed()) {
                $this->storage[$pos] = $schema->commitThis();
            }
        } elseif (!$schema instanceof IsUnmodifiable) {
            $type = \get_class($schema);
            throw new \InvalidArgumentException("Unable to commit the child[$pos] of type: $type");
        }
    }

    // ========================================================================

    private function addSchema(Schema $schema): void
    {
        $this->commitLastChildSchema();
        $this->storage[] = $schema;
    }

    /**
     * Add a schema to the list of child schemas.
     * 
     * @param self $schema
     *      The child schema to add.
     * 
     * @template S of self
     * 
     * @phpstan-param S $schema
     * @phpstan-return S
     */
    protected final function buildSchema(
        self $schema,
    ): self {
        $schema->parent = $this;
        $this->addSchema($schema);
        return $schema;
    }

    /**
     * @phpstan-return $this
     */
    protected final function addThenCommitSchemas(
        Schema ...$schemas,
    ): static {
        $this->commitLastChildSchema();

        foreach ($schemas as $schema) {
            $this->addSchema($schema);
        }
        $this->commitLastChildSchema();
        return $this;
    }

    /**
     * Creates a new unmodifiable schema from a validation closure
     * and add it to list of child schemas.
     * 
     * @param Closure(mixed):bool $validate
     *      Validate an element.
     *       - `$validate(mixed $element):bool`
     * 
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    protected final function buildSchemaFromClosure(
        Closure $validate,
    ): static {
        $this->addSchema(Schemas::fromClosure($validate));
        return $this;
    }

    // ========================================================================

    #[\Override]
    public function intersectionOf(Schema $schema, Schema $and, Schema ...$andMore): static
    {
        $this->addThenCommitSchemas(Schemas::schema(...[$schema, $and, ...$andMore]));
        return $this;
    }

    #[\Override]
    public function unionOf(Schema $schema, Schema $or, Schema ...$orMore): static
    {
        $this->addThenCommitSchemas(Schemas::union(...[$schema, $or, ...$orMore]));
        return $this;
    }

    #[\Override]
    public function negationOf(Schema $schema, Schema ...$andMoreNot): static
    {
        $this->addThenCommitSchemas(Schemas::negation(...[$schema, ...$andMoreNot]));
        return $this;
    }

    // ========================================================================

    #[\Override]
    public final function toInteger(): IntSchema
    {
        return $this->integer(true);
    }

    #[\Override]
    public final function toString(): StringSchema
    {
        return $this->string(true);
    }

    #[\Override]
    public final function toObject(): ObjectSchema
    {
        return $this->object(true);
    }

    #[\Override]
    public final function integer(bool $castToInt = false): IntSchema
    {
        return $this->buildSchema(new IntSchema($castToInt ? Functions::castToInt(...) : null));
    }

    #[\Override]
    public final function string(bool $castToString = false): StringSchema
    {
        return $this->buildSchema(new StringSchema($castToString ? Functions::castToString(...) : null));
    }

    #[\Override]
    public final function object(bool $castToObject = false): ObjectSchema
    {
        return $this->buildSchema(new ObjectSchema($$castToObject ? Functions::castToObject(...) : null));
    }

    // ========================================================================

    #[\Override]
    public final function isset(bool $isset = true): static
    {
        return $this->buildSchemaFromClosure(
            $isset
                ? fn(mixed $funValue) => $funValue !== null
                : fn(mixed $funValue) => $funValue === null
        );
    }

    #[\Override]
    public final function sameAs(mixed $value, mixed ...$orValue): static
    {
        $fn = empty($orValue)
            ? fn(mixed $fvalue) => $fvalue === $value
            : Closures::anyPredicate([$value, ...$orValue], Functions::areTheSame(...));

        return $this->buildSchemaFromClosure($fn);
    }

    #[\Override]
    public final function isOfType(string $type, string ...$orType): static
    {
        $fn = empty($orType)
            ? fn(mixed $value) => $type === \gettype($value)
            : Closures::anyPredicate([$type, ...$orType], fn($a, $b) => $a === \gettype($b));

        return $this->buildSchemaFromClosure($fn);
    }

    /**
     * @internal
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function dump(?callable $dump = null): static
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
}
