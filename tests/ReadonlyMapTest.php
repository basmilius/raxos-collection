<?php
declare(strict_types=1);

use Raxos\Collection\{Map, ReadonlyMap};

covers(ReadonlyMap::class);

it('merges into a new map and overrides duplicate keys', function (): void {
    $source = new ReadonlyMap(['a' => 1]);
    foreach ([['a' => 2, 'b' => null], new Map(['a' => 2, 'b' => null])] as $input) {
        $result = $source->merge($input);
        expect($result)->not->toBe($source)->and($result->toArray())->toBe(['a' => 2, 'b' => null])
            ->and($result->has('b'))->toBeTrue()->and($result->has('missing'))->toBeFalse()
            ->and($result->get('missing'))->toBeNull()->and($result->get('a'))->toBe(2)
            ->and(count($result))->toBe(2)->and(iterator_to_array($result))->toBe($result->toArray())
            ->and($result->jsonSerialize())->toBe($result->toArray())->and($result->__debugInfo())->toBe($result->toArray());
    }
    expect($source->toArray())->toBe(['a' => 1]);
});
