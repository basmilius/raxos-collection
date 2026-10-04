<?php
declare(strict_types=1);

namespace Raxos\Collection;

use ArrayAccess;
use ArrayIterator;
use Raxos\Collection\Error\CollectionImmutableException;
use Raxos\Contract\Collection\ValidatedArrayListInterface;
use Traversable;
use function count;

/**
 * Trait ArrayListAccessible
 *
 * @template TKey of array-key
 * @template TValue
 * @implements ArrayAccess<TKey, TValue>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection
 * @since 2.0.0
 */
trait ArrayListAccessible
{
    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset];
    }

    /**
     * {@inheritdoc}
     * @throws CollectionImmutableException
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function offsetSet(
        mixed $offset,
        mixed $value
    ): void
    {
        if ($this instanceof ReadonlyArrayList) {
            throw new CollectionImmutableException();
        }

        if ($this instanceof ValidatedArrayListInterface) {
            $this::validateItem($value);
        }

        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * {@inheritdoc}
     * @throws CollectionImmutableException
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function offsetUnset(mixed $offset): void
    {
        if ($this instanceof ReadonlyArrayList) {
            throw new CollectionImmutableException();
        }

        unset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
