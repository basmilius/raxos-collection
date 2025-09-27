<?php
declare(strict_types=1);

namespace Raxos\Collection;

use JsonSerializable;
use Raxos\Contract\{DebuggableInterface, SerializableInterface};
use Raxos\Contract\Collection\{ArrayListInterface, CollectionExceptionInterface, ValidatedArrayListInterface};
use Traversable;
use function is_subclass_of;
use function iterator_to_array;

/**
 * Class ArrayList
 *
 * @template TKey of array-key
 * @template TValue
 * @implements ArrayListInterface<TKey, TValue>
 * @mixin ArrayListable<TKey, TValue>
 * @mixin ArrayListAccessible<TKey, TValue>
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
    ) {}

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

    /**
     * Creates a new ArrayList instance with the given items.
     *
     * @template TOfKey
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
        $implementation = static::class;

        if ($items instanceof self) {
            $items = $items->data;
        } elseif ($items instanceof Traversable) {
            $items = iterator_to_array($items, false);
        }

        if (is_subclass_of($implementation, ValidatedArrayListInterface::class)) {
            foreach ($items as $item) {
                $implementation::validateItem($item);
            }
        }

        return new static($items);
    }

}
