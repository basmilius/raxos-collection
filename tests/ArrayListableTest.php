<?php
declare(strict_types=1);

use Raxos\Collection\{ArrayList, ArrayListable, IntArrayList, ReadonlyArrayList};

covers(ArrayListable::class);

it('passes original keys to visitors and predicates', function (): void {
    $list = new ArrayList(['a' => 1, 'b' => 2, 'c' => 3]);
    $visited = [];
    expect($list->each(function (int $value, string $key) use (&$visited): void {
        $visited[$key] = $value;
    }))->toBe($list)
        ->and($visited)->toBe($list->toArray())
        ->and($list->filter(static fn (int $value, string $key): bool => $key === 'b')->toArray())->toBe([2])
        ->and($list->first(static fn (int $value): bool => $value > 1))->toBe(2)
        ->and($list->last(static fn (int $value): bool => $value < 3))->toBe(2)
        ->and($list->last(static fn (int $value): bool => $value > 10, 'fallback'))->toBe('fallback');
});

it('preserves associative keys inside groups while exposing collection keys separately', function (): void {
    $list = new IntArrayList(['a' => 1, 'b' => 2, 'c' => 3]);
    $groups = $list->groupBy(static fn (int $value): int => $value % 2);
    expect($groups->toArray()[1]->toArray())->toBe(['a' => 1, 'c' => 3])
        ->and($list->keys()->toArray())->toBe(['a', 'b', 'c'])
        ->and($list->values()->toArray())->toBe([1, 2, 3])
        ->and($list->convertTo(ReadonlyArrayList::class))->toBeInstanceOf(ReadonlyArrayList::class);
});

it('collapses nested lists and projects objects that provide only', function (): void {
    $object = new class
    {
        public function only(array $keys): array
        {
            return array_intersect_key(['a' => 1, 'b' => 2], array_flip($keys));
        }
    };
    expect(new ArrayList([new ArrayList([1, 2]), [3], 4])->collapse()->toArray())->toBe([1, 2, 3, 4])
        ->and(new ArrayList([$object])->only(['a'])->toArray())->toBe([['a' => 1]]);
});

it('rejects an invalid chunk size without mutating the source', function (): void {
    $list = new ArrayList([1, 2]);
    expect(fn () => $list->chunk(0))->toThrow(ValueError::class)->and($list->toArray())->toBe([1, 2]);
});
