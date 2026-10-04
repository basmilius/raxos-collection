<?php
declare(strict_types=1);

use Raxos\Collection\{ArrayList, IntArrayList};
use Raxos\Collection\Error\CollectionInvalidTypeException;

covers(IntArrayList::class);

it('appends sequentially through array access and keeps type-changing transforms valid', function (): void {
    $list = new IntArrayList([1]);
    $list[] = 2;
    $list[] = 3;
    expect($list->toArray())->toBe([1, 2, 3])->and(json_encode($list))->toBe('[1,2,3]')
        ->and($list->map(static fn(int $value): string => (string)$value))->toBeInstanceOf(ArrayList::class)
        ->and($list->map(static fn(int $value): string => (string)$value)->toArray())->toBe(['1', '2', '3'])
        ->and($list->chunk(2)->first())->toBeInstanceOf(IntArrayList::class)
        ->and($list->groupBy(static fn(int $value): int => $value % 2)->count())->toBe(2)
        ->and($list->filter(static fn(int $value): bool => $value > 1))->toBeInstanceOf(IntArrayList::class);
});

it('accepts its element type and exposes its specialized operation', function (): void {
    $list = new IntArrayList([0, -1, 4]);
    expect($list->toArray())->toBe([0, -1, 4]);
    expect($list->sum())->toBe(3)->and(new IntArrayList()->sum())->toBe(0);
});

it('rejects invalid element types before any mutation takes effect', function (): void {
    foreach ([1.0, "1", null, false, [], new stdClass()] as $invalid) {
        $list = new IntArrayList([0, -1, 4]);
        expect(fn() => new IntArrayList([$invalid]))->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list->append($invalid))->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list->prepend($invalid))->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list[0] = $invalid)->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list->__unserialize([$invalid]))->toThrow(CollectionInvalidTypeException::class)
            ->and($list->toArray())->toBe([0, -1, 4]);
    }
});
