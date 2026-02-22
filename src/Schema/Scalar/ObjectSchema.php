<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Scalar;

use ReflectionObject;
use Time2Split\Help\Schema\Operator\AndSchema;
use Time2Split\Help\Schema\Reflection\ClassSchema;

/**
 * Validates an object element.
 * 
 * @package time2help\schema\scalar
 */
class ObjectSchema extends AndSchema
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
        return $this->buildSchema(new ClassSchema(transformElement: fn(object $object) => new ReflectionObject($object)));
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
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    final function is(object $object, object ...$orObject): static
    {
        return parent::sameAs($object, ...$orObject);
    }

    /**
     * Whether the object is an instance of a class.
     * 
     * @param string $class
     *      The class to be.
     * @return static
     *      `$this`.
     * 
     * @phpstan-return $this
     */
    public final function instanceOf(string $class): static
    {
        return $this->buildSchemaFromClosure(
            fn(mixed $funValue) => \is_object($funValue) && \is_a($funValue, $class),
        );
    }
}
