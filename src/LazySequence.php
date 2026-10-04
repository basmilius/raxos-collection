<?php
declare(strict_types=1);

namespace Raxos\Collection;

use Closure;
use Generator;
use Iterator;
use IteratorAggregate;
use Raxos\Error\InvalidArgumentException;
use Traversable;
use function count;
use function is_iterable;
use function iterator_to_array;

/**
 * Class LazySequence
 *
 * Deferred iteration; factories and arrays are repeatable, Iterator inputs are one-shot.
 *
 * @template TKey of array-key
 * @template TValue
 * @implements IteratorAggregate<TKey, TValue>
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection
 * @since 3.3.0
 */
final class LazySequence implements IteratorAggregate
{

    /**
     * Prevents a one-shot iterator from being silently reused after partial or complete iteration.
     *
     * @var bool
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private bool $consumed = false;

    /**
     * Retains the source factory without opening it until iteration begins.
     *
     * @param Closure():iterable<TKey, TValue> $factory
     * @param bool $repeatable
     *
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private function __construct(
        private readonly Closure $factory,
        private readonly bool $repeatable
    ) {}

    /**
     * Defers source access. Arrays and factories are repeatable; Iterator inputs can be consumed once.
     *
     * @template K of array-key
     * @template V
     * @param iterable<K, V>|callable():iterable<K, V> $source
     *
     * @return self<K, V>
     * @throws InvalidArgumentException
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public static function from(iterable|callable $source): self
    {
        if (is_iterable($source)) {
            return new self(static fn(): iterable => $source, !$source instanceof Iterator);
        }

        return new self(Closure::fromCallable($source), true);
    }

    /**
     * Opens the source on demand and rejects reuse of a one-shot iterator.
     *
     * @return Traversable<TKey, TValue>
     * @throws InvalidArgumentException
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function getIterator(): Traversable
    {
        if (!$this->repeatable && $this->consumed) {
            throw new InvalidArgumentException('This sequence has already consumed its one-shot iterator. Use a factory for repeatable iteration.');
        }

        $this->consumed = true;
        $source = ($this->factory)();

        if (!is_iterable($source)) {
            throw new InvalidArgumentException('A sequence factory must return an iterable.');
        }

        yield from $source;
    }

    /**
     * Transforms each value during iteration while retaining its source key.
     *
     * @template TResult
     * @param callable(TValue, TKey):TResult $fn
     *
     * @return self<TKey, TResult>
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function map(callable $fn): self
    {
        return self::from(function () use ($fn): Generator {
            foreach ($this as $key => $value) {
                yield $key => $fn($value, $key);
            }
        });
    }

    /**
     * Retains matching entries without buffering the source or changing their keys.
     *
     * @param callable(TValue, TKey):bool $predicate
     *
     * @return self<TKey, TValue>
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function filter(callable $predicate): self
    {
        return self::from(function () use ($predicate): Generator {
            foreach ($this as $key => $value) {
                if ($predicate($value, $key)) {
                    yield $key => $value;
                }
            }
        });
    }

    /**
     * Stops after the requested number of values without advancing the source to the next value.
     *
     * @param int $count
     *
     * @return self<TKey, TValue>
     * @throws InvalidArgumentException
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function take(int $count): self
    {
        if ($count < 0) {
            throw new InvalidArgumentException('The take count cannot be negative.');
        }

        return self::from(function () use ($count): Generator {
            if ($count === 0) {
                return;
            }

            $remaining = $count;

            foreach ($this as $key => $value) {
                yield $key => $value;

                if (--$remaining === 0) {
                    break;
                }
            }
        });
    }

    /**
     * Buffers at most one chunk and emits any remaining values as a final partial chunk.
     *
     * @param int $size
     *
     * @return self<int, list<TValue>>
     * @throws InvalidArgumentException
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function chunk(int $size): self
    {
        if ($size <= 0) {
            throw new InvalidArgumentException('The chunk size must be positive.');
        }

        return self::from(function () use ($size): Generator {
            $chunk = [];

            foreach ($this as $value) {
                $chunk[] = $value;

                if (count($chunk) === $size) {
                    yield $chunk;
                    $chunk = [];
                }
            }

            if ($chunk !== []) {
                yield $chunk;
            }
        });
    }

    /**
     * Consumes the sequence immediately and passes each value and its source key to the callback.
     *
     * @param callable(TValue, TKey):void $fn
     *
     * @return void
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function each(callable $fn): void
    {
        foreach ($this as $key => $value) {
            $fn($value, $key);
        }
    }

    /**
     * Materializes the remaining sequence; disabling key preservation retains duplicate-key values.
     *
     * @param bool $preserveKeys
     *
     * @return array<TKey, TValue>|list<TValue>
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function toArray(bool $preserveKeys = true): array
    {
        return iterator_to_array($this, $preserveKeys);
    }

}
