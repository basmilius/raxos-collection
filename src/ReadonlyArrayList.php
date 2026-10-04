<?php
declare(strict_types=1);

namespace Raxos\Collection;

use JsonSerializable;
use Raxos\Contract\Collection\ArrayListInterface;
use Raxos\Contract\Collection\CollectionExceptionInterface;
use Raxos\Contract\Collection\ValidatedArrayListInterface;
use Raxos\Contract\DebuggableInterface;
use Raxos\Contract\SerializableInterface;

/**
 * Class ReadonlyArrayList
 *
 * @template TKey of array-key
 * @template TValue
 * @implements ArrayListInterface<TKey, TValue>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection
 * @since 2.0.0
 */
readonly class ReadonlyArrayList implements ArrayListInterface, DebuggableInterface, JsonSerializable, SerializableInterface
{

    use ArrayListable;
    use ArrayListAccessible;

    /**
     * ArrayList constructor.
     *
     * @param array<TKey, TValue> $data
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public final function __construct(
        protected array $data = []
    )
    {
        if ($this instanceof ValidatedArrayListInterface) {
            foreach ($data as $item) {
                $this::validateItem($item);
            }
        }
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
        if ($this instanceof ValidatedArrayListInterface) {
            foreach ($data as $item) {
                $this::validateItem($item);
            }
        }

        $this->data = $data;
    }

    /**
     * Creates a new ArrayList instance with the given items.
     *
     * @template TOfKey of array-key
     * @template TOfValue
     *
     * @param iterable<TOfKey, TOfValue> $items
     *
     * @return static<TOfKey, TOfValue>
     * @throws CollectionExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public static function of(iterable $items): static
    {
        $data = [];

        foreach ($items as $key => $value) {
            $data[$key] = $value;
        }

        return new static($data);
    }
}
