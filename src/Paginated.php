<?php
declare(strict_types=1);

namespace Raxos\Collection;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Raxos\Contract\Collection\ArrayListInterface;

/**
 * Class Paginated
 *
 * @template TValue
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection
 * @since 2.0.0
 */
readonly class Paginated implements JsonSerializable
{

    /**
     * Paginated constructor.
     *
     * @param ArrayListInterface<int, TValue> $items
     * @param int $page
     * @param int $pageSize
     * @param int $pages
     * @param int $total
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public ArrayListInterface $items,
        public int $page,
        public int $pageSize,
        public int $pages,
        public int $total
    ) {}

    /**
     * {@inheritdoc}
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    #[ArrayShape([
        'items' => ArrayListInterface::class,
        'page' => 'int',
        'page_size' => 'int',
        'pages' => 'int',
        'total' => 'int'
    ])]
    public function jsonSerialize(): array
    {
        return [
            'items' => $this->items,
            'page' => $this->page,
            'page_size' => $this->pageSize,
            'pages' => $this->pages,
            'total' => $this->total
        ];
    }

}
