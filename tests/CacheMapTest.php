<?php
declare(strict_types=1);

use Raxos\Collection\CacheMap;

covers(CacheMap::class);

it('does not cache a failed factory and retries it on the next call', function (): void {
    $map = new CacheMap();
    $calls = 0;
    $factory = function () use (&$calls): string {
        if (++$calls === 1) {
            throw new RuntimeException('temporary');
        }

        return 'success';
    };
    expect(fn() => $map->remember('unit', $factory))->toThrow(RuntimeException::class)->and($map->has('unit'))->toBeFalse()
        ->and($map->remember('unit', $factory))->toBe('success')->and($map->remember('unit', $factory))->toBe('success')
        ->and($calls)->toBe(2);
    $map->unset('unit');
    expect($map->remember('unit', $factory))->toBe('success')->and($calls)->toBe(3);
});
