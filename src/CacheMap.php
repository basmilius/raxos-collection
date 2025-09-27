<?php
declare(strict_types=1);

namespace Raxos\Collection;

/**
 * Class CacheMap
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Collection
 * @since 2.0.0
 */
final class CacheMap extends Map
{

    /**
     * Remembers the result of the callable.
     *
     * @template TResult of mixed
     *
     * @param string $key
     * @param callable():TResult $fn
     *
     * @return TResult
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function remember(string $key, callable $fn): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $result = $fn();

        $this->set($key, $result);

        return $result;
    }

}
