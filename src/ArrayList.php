<?php
declare(strict_types=1);

namespace Raxos\Collection;

use JsonSerializable;
use Raxos\Contract\Collection\{ArrayListInterface, MutableArrayListInterface, ValidatedArrayListInterface};
use Raxos\Contract\{DebuggableInterface, SerializableInterface};
use Traversable;
use function array_is_list;
use function array_pop;
use function array_shift;
use function array_values;
use function iterator_to_array;

/**
 * Class ArrayList
 *
 * @template TKey of array-key
 * @template TValue
 * @implements ArrayListInterface<TKey, TValue>
 * @implements MutableArrayListInterface<TKey, TValue>
 * @mixin ArrayListable<TKey, TValue>
 * @mixin ArrayListAccessible<TKey, TValue>
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
                static::validateItem($item);
            }
        }
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function append(mixed $item): void
    {
        if ($this instanceof ValidatedArrayListInterface) {
            static::validateItem($item);
        }

        $this->data[] = $item;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function pop(): mixed
    {
        return array_pop($this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function prepend(mixed $item): void
    {
        if ($this instanceof ValidatedArrayListInterface) {
            static::validateItem($item);
        }

        $this->data = [$item, ...$this->data];
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function shift(): mixed
    {
        return array_shift($this->data);
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
                static::validateItem($item);
            }
        }

        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public static function of(iterable $items): static
    {
        if ($items instanceof self) {
            $items = $items->data;
        } elseif ($items instanceof Traversable) {
            $items = iterator_to_array($items);
        }

        if (array_is_list($items)) {
            $items = array_values($items);
        }

        return new static($items);
    }

}
