<?php

declare(strict_types=1);

namespace Time2Split\Help;

/**
 * A container which may contain a value.
 * 
 * The class is inspired by that of Java, but contrary to it it allows null values.
 *
 * @author Olivier Rodriguez (zuri)
 * 
 * @template T
 */
final class Optional
{
    private mixed $value;

    private bool $isReference;

    private bool $isPresent;

    /**
     * @return Optional<void>
     */
    private function __construct()
    {
        $this->isPresent = false;
        $this->isReference = false;
    }

    /**
     * @template TT
     * @phpstan-param TT $value
     * @phpstan-self-out self<TT>
     */
    private function setValue(mixed $value): void
    {
        $this->isReference = false;
        $this->isPresent = true;
        $this->value = $value;
    }

    /**
     * @template TT
     * @phpstan-param TT &$value
     * @phpstan-self-out self<TT>
     */
    private function setValueRef(mixed &$value): void
    {
        $this->isReference = true;
        $this->isPresent = true;
        $this->value = &$value;
    }

    /**
     * Returns an Optional containing a specified value.
     * 
     * @param mixed $value
     *      The value to be stored.
     * @return Optional
     *      An Optional containing `$value`.
     * 
     * @phpstan-param T $value
     * @phpstan-return Optional<T>
     */
    public static function of(mixed $value): self
    {
        $opt = new Optional();
        $opt->setValue($value);
        return $opt;
    }

    /**
     * Returns an Optional containing a reference to a specified value.
     * 
     * @param mixed &$value
     *      The reference to the value to be stored.
     * @return Optional
     *      An Optional containing `$value`.
     * 
     * @phpstan-param T &$value
     * @phpstan-return Optional<T>
     */
    public static function ofRef(mixed &$value): self
    {
        $opt = new Optional();
        $opt->setValueRef($value);
        return $opt;
    }

    /**
     * Gets an Optional of a specified value if non-null, otherwise returns an empty Optional.
     * 
     * @param mixed $value 
     *      The possibly-null value to describe.
     * @param mixed $null
     *      The value to be considered as null.
     * @return Optional
     *      An Optional containing `$value` if `$value !== $null`,
     *      otherwise {@see Optional::empty()}.
     * 
     * @template N
     * 
     * @phpstan-param T|N $value
     * @phpstan-param N $null
     * @phpstan-return Optional<T>
     */
    public static function ofNullable(mixed $value, mixed $null = null): self
    {
        if ($value === $null) {
            return self::empty();
        }
        /** @phpstan-var T $value */
        return self::of($value);
    }

    /**
     * Gets an Optional of a specified value if non-null, otherwise returns an empty Optional.
     * 
     * @param mixed &$value 
     *      The possibly-null reference to the value to describe.
     * @param mixed $null
     *      The value to be considered as null.
     * @return Optional
     *      An Optional containing `$value` if `$value !== $null`,
     *      otherwise {@see Optional::empty()}.
     * 
     * @template N
     * 
     * @phpstan-param T|N $value
     * @phpstan-param N $null
     * @phpstan-return Optional<T>
     */
    public static function ofNullableRef(mixed &$value, mixed $null = null): self
    {
        if ($value === $null) {
            return self::empty();
        }
        /** @phpstan-var T $value */
        return self::ofRef($value);
    }

    /**
     * @var Optional<T>
     */
    private static Optional $empty;

    /**
     * Returns an empty Optional singleton instance (ie. no value is stored).
     * 
     * The value is a singleton and may be compared with the `===` operator.
     * 
     * @return Optional
     *      An empty Optional.
     * 
     * @phpstan-return Optional<T>
     */
    public static function empty(): self
    {
        return self::$empty ??= new self();
    }

    // ========================================================================

    /**
     * Whether a value is stored in this Optional.
     * 
     * @return bool true if there is a stored value, otherwise false.
     */
    public final function isPresent(): bool
    {
        return $this->isPresent;
    }

    /**
     * Whether this Optional stores no value.
     * 
     * @return bool true if there is no stored value, otherwise false.
     */
    public final function isEmpty(): bool
    {
        return !$this->isPresent;
    }

    /**
     * Whether a reference to a value is stored in this Optional.
     * 
     * @return bool true if there is a stored reference, otherwise false.
     */
    public final function isReference(): bool
    {
        return $this->isReference;
    }

    /**
     * Retrieves the value of this Optional,
     * or throws an error if no value is stored.
     * 
     * @return mixed
     *      The value of the optional.
     * @throws \Error
     *      If no value is stored.
     * 
     * @phpstan-return T
     */
    public final function get(): mixed
    {
        if ($this->isPresent())
            return $this->value;

        throw new \Error('An empty Optional cannot get a value');
    }

    /**
     * Retrieves a reference to the value of this Optional,
     * or throws an error if no value is stored.
     * 
     * @return mixed
     *      The value of the optional.
     * @throws \Error
     *      If no value is stored.
     * 
     * @phpstan-return T
     */
    public final function &getRef(): mixed
    {
        if ($this->isPresent()) {

            if (!$this->isReference)
                throw new \Error('The Optional does not store a reference');

            return $this->value;
        }
        throw new \Error('An empty Optional cannot get a value');
    }

    /**
     * Returns the value if present, otherwise another specified one.
     * 
     * @param mixed $other The value to be returned if this Optional is empty.
     * It may be null.
     * 
     * @return mixed
     *      The value if present,
     *      otherwise `$other`.
     * 
     * @template O
     * 
     * @phpstan-param O $other
     * @phpstan-return T|O
     */
    public final function orElse(mixed $other): mixed
    {
        if ($this->isPresent)
            return $this->value;

        return $other;
    }

    /**
     * Returns the value if present, otherwise the result of a closure.
     * 
     * @param \Closure $supplier
     * - `$supplier():mixed`
     * 
     * Compute a value to be returned if this Optional is empty.
     * 
     * @return mixed
     *      The value if present,
     *      otherwise the result of `$supplier()`.
     * 
     * @template S
     * 
     * @phpstan-param \Closure():S $supplier
     * @phpstan-return T|S
     */
    public final function orElseGet(\Closure $supplier): mixed
    {
        if ($this->isPresent)
            return $this->value;

        return $supplier();
    }
}
