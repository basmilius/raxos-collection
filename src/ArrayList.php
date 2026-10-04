<?php
declare(strict_types=1);

namespace Raxos\Collection;

use JsonSerializable;
use Raxos\Contract\Collection\ArrayListInterface;
use Raxos\Contract\Collection\MutableArrayListInterface;
use Raxos\Contract\Collection\ValidatedArrayListInterface;
use Raxos\Contract\DebuggableInterface;
use Raxos\Contract\SerializableInterface;
use function array_pop;
use function array_shift;

/**
 * Class ArrayList
 *
 * Stores an eager mutable sequence while preserving keys and validating typed-list values.
 *
 * @template TKey of array-key
 * @template TValue
 * @implements ArrayListInterface<TKey, TValue>
 * @implements MutableArrayListInterface<TKey, TValue>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection
 * @since 2.0.0
 */
class ArrayList implements ArrayListInterface, MutableArrayListInterface, DebuggableInterface, JsonSerializable, SerializableInterface
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
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function append(mixed $item): void
    {
        if ($this instanceof ValidatedArrayListInterface) {
            $this::validateItem($item);
        }

        $this->data[] = $item;
    }

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function pop(): mixed
    {
        return array_pop($this->data);
    }

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function prepend(mixed $item): void
    {
        if ($this instanceof ValidatedArrayListInterface) {
            $this::validateItem($item);
        }

        $this->data = [$item, ...$this->data];
    }

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function shift(): mixed
    {
        return array_shift($this->data);
    }

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __debugInfo(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __serialize(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     *
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
     * {@inheritdoc}
     *
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
