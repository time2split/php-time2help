<?php

declare(strict_types=1);

namespace Time2Split\Help\Schema;

/**
 * A schema being builded.
 * 
 * @package time2help\schema
 */
interface BuildingSchema extends Schema
{
    /**
     * Gets the parent schema.
     * 
     * @param int $nb
     *      The (positive) number of parents to get through.
     * @param string $returnsClass
     *      The return type of the method.
     *      (Only usefull for the static type analyser phpstan)
     * @return BuildingSchema&OfSchemas
     *      The parent schema,
     * 
     * @template S of BuildingSchema&OfSchemas
     * 
     * @phpstan-param class-string<S>|null $returnsClass
     * @phpstan-return ($returnsClass is null ? BuildingSchema&OfSchemas : S)
     */
    function up(int $nb = 1, ?string $returnsClass = null): BuildingSchema&OfSchemas;

    /**
     * Get the root schema.
     * 
     * @param string $returnsClass
     *      The return type of the method.
     *      (Only usefull for the static type analyser phpstan)
     * @return BuildingSchema&OfSchemas
     *      The root schema,
     * 
     * @template S of BuildingSchema&OfSchemas
     * 
     * @phpstan-param class-string<S>|null $returnsClass
     * @phpstan-return ($returnsClass is null ? BuildingSchema&OfSchemas : S)
     */
    function top(?string $returnsClass = null): BuildingSchema&OfSchemas;
}
