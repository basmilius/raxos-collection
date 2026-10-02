<?php
declare(strict_types=1);

use Raxos\Collection\{Map, ReadonlyMap};

covers(Map::class);

it('distinguishes missing keys from stored falsey values', function (): void {
    $map = new Map(['null' => null, 'false' => false, 'zero' => 0, 'empty' => '']);
    expect($map->has('null'))->toBeTrue()->and($map->has('missing'))->toBeFalse()
        ->and($map->get('missing', 'fallback'))->toBe('fallback')->and($map->get('false', true))->toBeFalse()
        ->and($map->get('zero', 1))->toBe(0)->and($map->get('empty', 'fallback'))->toBe('');
});

it('merges missing keys while preserving existing keys including null', function (): void {
    $map = new Map(['a' => null]);
    expect($map->merge(new ReadonlyMap(['a' => 2, 'b' => 3])))->toBe($map)
        ->and($map->toArray())->toBe(['a' => null, 'b' => 3]);
    $map->set('b', 4);
    $map->unset('a');
    expect(count($map))->toBe(1)->and(iterator_to_array($map))->toBe(['b' => 4])
        ->and($map->__debugInfo())->toBe(['b' => 4])->and($map->jsonSerialize())->toBe(['b' => 4]);
});
