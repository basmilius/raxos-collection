<?php
declare(strict_types=1);

use Raxos\Collection\ArrayList;
use Raxos\Collection\Error\CollectionImmutableException;
use Raxos\Collection\Error\CollectionInvalidTypeException;
use Raxos\Collection\IntArrayList;
use Raxos\Collection\ReadonlyArrayList;

it('appends through array access with sequential numeric keys', function (): void {
    $list = new IntArrayList([1]);
    $list[] = 2;
    $list[] = 3;
    expect($list->toArray())->toBe([1, 2, 3]);
    expect(json_encode($list))->toBe('[1,2,3]');
});

it('validates every mutation and deserialization entry point', function (string $operation): void {
    $list = new IntArrayList([1]);
    expect(function () use ($list, $operation): void {
        match ($operation) {
            'append' => $list->append('bad'),
            'prepend' => $list->prepend('bad'),
            'offset' => $list[0] = 'bad',
            'unserialize' => $list->__unserialize(['bad']),
            'construct' => new IntArrayList(['bad']),
        };
    })->toThrow(CollectionInvalidTypeException::class);
    expect($list->toArray())->toBe([1]);
})->with(['append', 'prepend', 'offset', 'unserialize', 'construct']);

it('allows type-changing transforms without putting invalid values in typed lists', function (): void {
    $list = new IntArrayList([1,2,3]);
    expect($list->map(static fn(int $value) => (string)$value))->toBeInstanceOf(ArrayList::class);
    expect($list->map(static fn(int $value) => (string)$value)->toArray())->toBe(['1','2','3']);
    expect($list->chunk(2)->first())->toBeInstanceOf(IntArrayList::class);
    expect($list->groupBy(static fn(int $value) => $value % 2)->count())->toBe(2);
    expect($list->filter(static fn(int $value) => $value > 1))->toBeInstanceOf(IntArrayList::class);
});

it('preserves read only transformations and rejects mutation', function (): void {
    $list = new ReadonlyArrayList([1]);
    expect($list->map(static fn(int $value) => (string)$value))->toBeInstanceOf(ReadonlyArrayList::class);
    expect(fn() => $list[] = 2)->toThrow(CollectionImmutableException::class);
});
