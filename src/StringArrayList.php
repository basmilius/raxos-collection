<?php
declare(strict_types=1);

namespace Raxos\Collection;

use Raxos\Collection\Error\CollectionInvalidTypeException;
use Raxos\Contract\Collection\ValidatedArrayListInterface;
use function implode;
use function is_string;
use function preg_replace;

/**
 * Class StringArrayList
 *
 * @extends ArrayList<int, string>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection
 * @since 2.0.0
 */
class StringArrayList extends ArrayList implements ValidatedArrayListInterface
{

    /**
     * Glues the strings together with commas and replaces the last one with
     * an ampersand.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function commaCommaAnd(): string
    {
        return preg_replace('/(.*),/', '$1 &', implode(', ', $this->data));
    }

    /**
     * Glues the strings together using the given glue.
     *
     * @param string $glue
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function join(string $glue = ', '): string
    {
        return implode($glue, $this->data);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public static function validateItem(mixed $item): void
    {
        if (!is_string($item)) {
            throw new CollectionInvalidTypeException(static::class, 'string');
        }
    }

}
