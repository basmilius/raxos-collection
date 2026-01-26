<?php
declare(strict_types=1);

namespace Raxos\Collection;

use Raxos\Contract\Collection\{ArrayableInterface, ArrayListInterface};
use Raxos\Database\Orm\Model;
use Raxos\Foundation\Util\ArrayUtil;
use function array_all;
use function array_any;
use function array_chunk;
use function array_column;
use function array_diff;
use function array_filter;
use function array_is_list;
use function array_key_first;
use function array_key_last;
use function array_keys;
use function array_map;
use function array_merge;
use function array_reduce;
use function array_reverse;
use function array_search;
use function array_slice;
use function array_splice;
use function array_unique;
use function array_values;
use function count;
use function in_array;
use function is_array;
use function is_callable;
use function shuffle;
use function usort;
use const ARRAY_FILTER_USE_BOTH;

/**
 * Trait ArrayListable
 *
 * @template TKey of array-key
 * @template TValue
 * @implements ArrayListInterface<TKey, TValue>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection
 * @since 2.0.0
 */
trait ArrayListable
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function chunk(int $size): static
    {
        $chunks = array_chunk($this->data, $size);

        return new static(array_map(static fn(array $chunk) => new static($chunk), $chunks));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function clone(): static
    {
        return new static($this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function collapse(): static
    {
        $result = [];

        foreach ($this->data as $item) {
            if ($item instanceof self) {
                $item = $item->data;
            }

            if (is_array($item)) {
                foreach ($item as $subItem) {
                    $result[] = $subItem;
                }
            } else {
                $result[] = $item;
            }
        }

        return new static($result);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function column(string|int ...$columns): static
    {
        $result = $this->data;

        foreach ($columns as $column) {
            $result = array_column($result, $column);
        }

        return new static($result);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function contains(mixed $item): bool
    {
        if (is_callable($item)) {
            return $this->first($item) !== null;
        }

        return in_array($item, $this->data, true);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function convertTo(string $implementation): ArrayListInterface
    {
        return new $implementation($this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function diff(iterable $items): static
    {
        return new static(array_diff($this->data, ArrayUtil::ensureArray($items)));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function each(callable $fn): static
    {
        foreach ($this->data as $key => $item) {
            $fn($item, $key);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function every(callable $predicate): bool
    {
        return array_all($this->data, $predicate);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function filter(callable $predicate): static
    {
        return new static(array_values(array_filter($this->data, $predicate, ARRAY_FILTER_USE_BOTH)));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function first(?callable $predicate = null, mixed $default = null): mixed
    {
        return ArrayUtil::first($this->data, $predicate, $default);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function firstKey(): string|int|null
    {
        return array_key_first($this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function groupBy(callable $fn): static
    {
        $groups = [];
        $isList = array_is_list($this->data);

        foreach ($this->data as $key => $value) {
            $group = $fn($value, $key);

            if ($isList) {
                $groups[$group][] = $value;
            } else {
                $groups[$group][$key] = $value;
            }
        }

        return new static(array_map(static fn(array $group) => new static($group), $groups));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function isNotEmpty(): bool
    {
        return !empty($this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function keys(): static
    {
        return new self(array_keys($this->data));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function last(?callable $predicate = null, mixed $default = null): mixed
    {
        if ($predicate === null) {
            return count($this->data) > 0 ? ArrayUtil::last($this->data) : $default;
        }

        return ArrayUtil::last($this->data, $predicate, $default);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function lastKey(): string|int|null
    {
        return array_key_last($this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function map(callable $fn): static
    {
        return new static(array_map($fn, $this->data));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function merge(ArrayableInterface|self|iterable $items): static
    {
        return new static(array_merge($this->data, ArrayUtil::ensureArray($items)));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function only(array $keys): static
    {
        return $this->map(static function (mixed $item) use ($keys) {
            if (is_array($item)) {
                return ArrayUtil::only($item, $keys);
            }

            if ($item instanceof Model) {
                return $item->only($keys);
            }

            return $item;
        });
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function reduce(callable $fn, mixed $initial = null): mixed
    {
        return array_reduce($this->data, $fn, $initial);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function reverse(): static
    {
        return new static(array_reverse($this->data));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function search(mixed $value): string|int|null
    {
        return ($result = array_search($value, $this->data, true)) !== false ? $result : null;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function shuffle(): static
    {
        $items = [...$this->data];

        shuffle($items);

        return new static($items);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function slice(int $offset, ?int $length = null): static
    {
        return new static(array_slice($this->data, $offset, $length));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function some(callable $predicate): bool
    {
        return array_any($this->data, $predicate);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function sort(callable $compare): static
    {
        $data = $this->data;

        usort($data, $compare);

        return new static($data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function splice(int $offset = 0, int $length = 0, ...$replacement): static
    {
        $data = $this->data;

        array_splice($data, $offset, $length, $replacement);

        return new static($data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function unique(): static
    {
        return new static(array_values(array_unique($this->data)));
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function values(): static
    {
        return new static(array_values($this->data));
    }

}
