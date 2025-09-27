<?php
declare(strict_types=1);

namespace Raxos\Collection;

use Raxos\Collection\Error\CollectionInvalidTypeException;
use Raxos\Contract\Collection\ValidatedArrayListInterface;
use function array_sum;
use function is_float;
use function is_int;

/**
 * Class NumberArrayList
 *
 * @extends ArrayList<int, int|float>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection
 * @since 2.0.0
 */
class NumberArrayList extends ArrayList implements ValidatedArrayListInterface
{

    /**
     * Sums the items of the array list.
     *
     * @return float|int
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function sum(): float|int
    {
        return array_sum($this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public static function validateItem(mixed $item): void
    {
        if (!is_int($item) && !is_float($item)) {
            throw new CollectionInvalidTypeException(static::class, 'number');
        }
    }

}
