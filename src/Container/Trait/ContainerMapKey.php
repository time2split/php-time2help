<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Closure;

/**
 * Add a mapping to transform any type of key used for accessing to an element
 * into a valid array key.
 * 
 * It maps a key before an assignation.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container\class
 * 
 * @template K
 * @template KMAP
 * @template V
 */
trait ContainerMapKey
{
    /**
     * @phpstan-var Closure(K):KMAP
     * Closure $mapKey
     *  - `mapKey(K key):string|int`
     */
    protected Closure $mapKey;

    /**
     * @var array<K>
     */
    private array $mapKeyIndex = [];

    /**
     * Closure $mapKey
     *  - `mapKey(mixed key):string|int`
     *  Transform an incoming key into a valid array key..
     */
    protected function setMapKey(Closure $mapKey): void
    {
        $this->mapKey = $mapKey;
    }

    protected function copyMapKeyInternals(object $subject): void
    {
        /* @phpstan-ignore property.notFound */
        $this->mapKeyIndex = $subject->mapKeyIndex;
    }

    // ========================================================================
    // Redefine ArrayAccess methods

    /**
     * @param K $offset
     */
    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        return $this->mapKeyOffsetExists($offset);
    }

    /**
     * @param K $offset
     */
    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->mapKeyOffsetGet($offset);
    }

    /**
     * @param K $offset
     * @param V $value
     */
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->mapKeyOffsetSet($offset, $value);
    }

    /**
     * @param K $offset
     */
    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        $this->mapKeyOffsetUnset($offset);
    }

    /**
     * @return \Traversable<K,V>
     */
    #[\Override]
    public function getIterator(): \Traversable
    {
        return $this->mapKeyIterator(parent::getIterator());
    }

    // ========================================================================
    // Internal function to help redefinition

    public function mapKeyOffsetExists(mixed $offset): bool
    {
        return parent::offsetExists(($this->mapKey)($offset));
    }

    protected function mapKeyOffsetGet(mixed $offset): mixed
    {
        return parent::offsetGet(($this->mapKey)($offset));
    }

    protected function mapKeyOffsetSet(mixed $offset, mixed $value): void
    {
        if (null === $offset) {
            parent::offsetSet(null, $value);
        } else {
            parent::offsetSet($k = ($this->mapKey)($offset), $value);
            $this->mapKeyIndex[$k] = $offset;
        }
    }

    protected function mapKeyOffsetUnset(mixed $offset): void
    {
        $k = ($this->mapKey)($offset);
        parent::offsetUnset($k);
        unset($this->mapKeyIndex[$k]);
    }

    /**
     * @param iterable<K,V> $iterable
     * @return \Traversable<KMAP,V>
     */
    protected final function mapKeyIterator(iterable $iterable): \Traversable
    {
        foreach ($iterable as $k => $v) {
            $mappedKey  = $this->mapKeyIndex[$k] ?? null;
            yield $mappedKey => $v;
        }
    }
}
