<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema\Reflection;

use ReflectionClass;
use Time2Split\Help\Schema\Impl\AbstractSchemaOfSchema;
use Time2Split\Help\Schema\StringSchema;

/**
 * Validates an object element.
 * 
 * @package time2help\schema
 */
class ClassSchema extends AbstractSchemaOfSchema
{
    #[\Override]
    public function validateElement($element): bool
    {
        if (\is_string($element))
            $element = new ReflectionClass($element);
        elseif (\is_object($element)) {

            if (!$element instanceof ReflectionClass)
                $element = new ReflectionClass(\get_class($element));
        }
        return parent::validateElement($element);
    }

    // ========================================================================

    /**
     * Gets a string schema on the class short name.
     * 
     * @return StringSchema
     *      The schema.
     */
    public function shortName(): StringSchema
    {
        return $this->buildSchema(new StringSchema($this, fn(ReflectionClass $fn) => $fn->getShortName()));
    }

    /**
     * Gets a string schema on the class namespace.
     * 
     * @return StringSchema
     *      The schema.
     */
    public function namespace(): StringSchema
    {
        return $this->buildSchema(new StringSchema($this, fn(ReflectionClass $fn) => $fn->getNamespaceName()));
    }

    /**
     * Gets a string schema on the class name ($shortName\$namespace).
     * 
     * @return StringSchema
     *      The schema.
     */
    public function name(): StringSchema
    {
        return $this->buildSchema(new StringSchema($this, fn(ReflectionClass $fn) => $fn->getName()));
    }
}
