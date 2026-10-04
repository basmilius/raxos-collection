<?php
declare(strict_types=1);

namespace Raxos\Collection;

use JsonSerializable;
use Raxos\Contract\Collection\ArrayListInterface;

/**
 * Class CursorPage
 *
 * A forward page without a total-count query.
 *
 * @template TValue
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection
 * @since 3.3.0
 */
final readonly class CursorPage implements JsonSerializable
{
    /**
     * Carries a continuation only when another page is available; no total is calculated.
     *
     * @param ArrayListInterface<int, TValue> $items
     * @param string|null $nextCursor
     * @param bool $hasMore
     *
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function __construct(
        public ArrayListInterface $items,
        public ?string $nextCursor,
        public bool $hasMore
    ) {}

    /**
     * Uses the same snake-case continuation fields as the generated API schema.
     *
     * @return array{items:ArrayListInterface<int, TValue>, next_cursor:string|null, has_more:bool}
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function jsonSerialize(): array
    {
        return ['items' => $this->items, 'next_cursor' => $this->nextCursor, 'has_more' => $this->hasMore];
    }
}
