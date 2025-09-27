<?php
declare(strict_types=1);

namespace Raxos\Collection;

use ArrayIterator;
use JsonSerializable;
use Raxos\Contract\{DebuggableInterface, SerializableInterface};
use Raxos\Contract\Collection\{MapInterface, MutableMapInterface};
use Traversable;
use function array_key_exists;
use function count;

/**
 * Class Map
 *
 * @template TValue
 * @implements MapInterface<TValue>
 * @implements MutableMapInterface<TValue>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection
 * @since 2.0.0
 */
class Map implements DebuggableInterface, MapInterface, MutableMapInterface, JsonSerializable, SerializableInterface
{

    /**
     * Map constructor.
     *
     * @param array<string, TValue> $data
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        protected array $data = []
    ) {}

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function unset(string $key): void
    {
        unset($this->data[$key]);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function merge(MapInterface|array $other): static
    {
        if ($other instanceof MapInterface) {
            $other = $other->toArray();
        }

        $this->data += $other;

        return $this;
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
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __serialize(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __unserialize(array $data): void
    {
        $this->data = $data;
    }

}
