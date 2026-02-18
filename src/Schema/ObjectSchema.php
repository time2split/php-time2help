<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema;

use ReflectionObject;
use Time2Split\Help\Schema\Impl\AbstractSchemaOfSchema;
use Time2Split\Help\Schema\Reflection\ClassSchema;

/**
 * Validates an object element.
 * 
 * @package time2help\schema
 */
class ObjectSchema extends AbstractSchemaOfSchema
{
    #[\Override]
    public function validateElement($element): bool
    {
        if (!\is_object($element))
            return false;

        return parent::validateElement($element);
    }

    // ========================================================================

    /**
     * Gets a class schema on the object class.
     * 
     * @return ClassSchema
     *      The schema.
     */
    public function class(): ClassSchema
    {
        return $this->buildSchema(new ClassSchema($this, fn(object $object) => new ReflectionObject($object)));
    }

    // ========================================================================

    /**
     * Whether the object is identical to another one.
     * 
     * (Uses the `===` operator.)
     * 
     * @param object $object
     *      The object to be compared to.
     * @param object ...$orObject
     *      More objects to be compared to.
     * @return Schema&OfSchemas
     *      Its parent schema,
     *      or `$this` if there is no parent.
     */
    final function is(object $object, object ...$orObject): Schema&OfSchemas
    {
        return parent::sameAs($object, ...$orObject);
    }

    /**
     * Whether the object is an instance of a class.
     * 
     * @param string $class
     *      The class to be.
     * @return Schema&OfSchemas
     *      Its parent schema,
     *      or `$this` if there is no parent.
     */
    public final function instanceOf(string $class): Schema&OfSchemas
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => \is_object($funValue) && \is_a($funValue, $class),
        );
    }
}
