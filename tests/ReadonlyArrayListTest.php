<?php
declare(strict_types=1);

use Raxos\Collection\ReadonlyArrayList;

covers(ReadonlyArrayList::class);

it('keeps transformed collections immutable', function (): void {
    $list = new ReadonlyArrayList([1]);
    $mapped = $list->map(static fn(int $value): string => (string)$value);
    expect($mapped)->toBeInstanceOf(ReadonlyArrayList::class)->and($mapped->toArray())->toBe(['1'])
        ->and(fn() => $mapped[] = '2')->toThrow(Raxos\Collection\Error\CollectionImmutableException::class);
});

it('creates independent immutable copies from all iterable forms', function (): void {
    $list = new ReadonlyArrayList(['a' => 0, 'b' => null]);
    foreach ([$list, $list->toArray(), new ArrayIterator($list->toArray())] as $input) {
        $copy = ReadonlyArrayList::of($input);
        expect($copy)->not->toBe($list)->and($copy->toArray())->toBe($list->toArray())
            ->and($copy->__debugInfo())->toBe($list->toArray())
            ->and(unserialize(serialize($copy))->toArray())->toBe($list->toArray());
    }
    expect(ReadonlyArrayList::of([1, 2])->toArray())->toBe([1, 2]);
});
