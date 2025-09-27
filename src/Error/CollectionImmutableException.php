<?php
declare(strict_types=1);

namespace Raxos\Collection\Error;

use Raxos\Contract\Collection\CollectionExceptionInterface;
use Raxos\Error\Exception;

/**
 * Class CollectionImmutableException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection\Error
 * @since 2.0.0
 */
final class CollectionImmutableException extends Exception implements CollectionExceptionInterface
{

    /**
     * CollectionImmutableException constructor.
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct()
    {
        parent::__construct(
            'collection_immutable',
            'The collection is immutable.'
        );
    }

}
