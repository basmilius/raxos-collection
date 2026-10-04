<?php
declare(strict_types=1);

use Raxos\Collection\Error\CollectionInvalidTypeException;
use Raxos\Collection\NumberArrayList;

covers(NumberArrayList::class);

it('accepts its element type and exposes its specialized operation', function (): void {
    $list = new NumberArrayList([0, -1, 4.5]);
    expect($list->toArray())->toBe([0, -1, 4.5]);
    expect($list->sum())->toBe(3.5)->and(new NumberArrayList()->sum())->toBe(0);
});

it('rejects invalid element types before any mutation takes effect', function (): void {
    foreach (["1", null, false, [], new stdClass()] as $invalid) {
        $list = new NumberArrayList([0, -1, 4.5]);
        expect(fn() => new NumberArrayList([$invalid]))->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list->append($invalid))->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list->prepend($invalid))->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list[0] = $invalid)->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list->__unserialize([$invalid]))->toThrow(CollectionInvalidTypeException::class)
            ->and($list->toArray())->toBe([0, -1, 4.5]);
    }
});
