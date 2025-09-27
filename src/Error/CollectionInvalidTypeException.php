<?php
declare(strict_types=1);

namespace Raxos\Collection\Error;

use Raxos\Contract\Collection\CollectionExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class CollectionInvalidTypeException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection\Error
 * @since 2.0.0
 */
final class CollectionInvalidTypeException extends Exception implements CollectionExceptionInterface
{

    /**
     * CollectionInvalidTypeException constructor.
     *
     * @param string $class
     * @param string $expected
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        public readonly string $class,
        public readonly string $expected
    )
    {
        parent::__construct(
            'collection_invalid_type',
            "{$this->class} only accepts items of type {$this->expected}."
        );
    }

}
