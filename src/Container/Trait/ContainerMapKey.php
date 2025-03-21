<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Time2Split\Help\Container\Container;

/**
 * Add a mapping to the keys.
 * 
 * It maps a key before an assignation.
 * 
 * @var array $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 */
trait ContainerMapKey
{
    protected $mapKey;

    private $mapKeyIndex = [];

    protected function setMapKey(callable $mapKey): void
    {
        $this->mapKey = $mapKey;
    }

    protected function copyMapKeyInternals(Container $subject): void
    {
        $this->mapKeyIndex = $subject->mapKeyIndex;
    }

    // ========================================================================
    // Redefine ArrayAccess methods

    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        return $this->mapKeyOffsetExists($offset);
    }
    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->mapKeyOffsetGet($offset);
    }
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->mapKeyOffsetSet($offset, $value);
    }
    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        $this->mapKeyOffsetUnset($offset);
    }
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
        parent::offsetSet($k = ($this->mapKey)($offset), $value);
        $this->mapKeyIndex[$k] = $offset;
    }

    protected function mapKeyOffsetUnset(mixed $offset): void
    {
        $k = ($this->mapKey)($offset);
        parent::offsetUnset($k);
        unset($this->mapKeyIndex[$k]);
    }

    protected final function mapKeyIterator(iterable $iterable): \Traversable
    {
        foreach ($iterable as $k => $v) {
            $mappedKey = $this->mapKeyIndex[$k];
            yield $mappedKey => $v;
        }
    }
}
