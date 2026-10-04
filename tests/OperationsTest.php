<?php
declare(strict_types=1);

use Raxos\Collection\Error\CollectionInvalidTypeException;
use Raxos\Collection\{ArrayList, CacheMap, IntArrayList, Map, NumberArrayList, Paginated, ReadonlyArrayList, ReadonlyMap, StringArrayList};

it('keeps value-preserving transforms independent and typed', function (): void {
    $list = new IntArrayList([3, 1, 2, 1]);
    expect($list->sort(static fn(int $a, int $b): int => $a <=> $b)->toArray())->toBe([1, 1, 2, 3])
        ->and($list->unique()->toArray())->toBe([3, 1, 2])
        ->and($list->diff([1])->values()->toArray())->toBe([3, 2])
        ->and($list->reverse()->toArray())->toBe([1, 2, 1, 3])
        ->and($list->slice(1, 2)->toArray())->toBe([1, 2])
        ->and($list->splice(1, 2, 9)->toArray())->toBe([3, 9, 1])
        ->and($list->merge(new ArrayIterator([4]))->toArray())->toBe([3, 1, 2, 1, 4])
        ->and($list->clone())->not->toBe($list)
        ->and($list->toArray())->toBe([3, 1, 2, 1])
        ->and($list->shuffle()->count())->toBe(4)
        ->and($list->reduce(static fn(int $total, int $value): int => $total + $value, 0))->toBe(7);
});

it('handles empty collections and strict searches', function (): void {
    $empty = new ArrayList();
    expect($empty->isEmpty())->toBeTrue()->and($empty->isNotEmpty())->toBeFalse()
        ->and($empty->first(default: 'empty'))->toBe('empty')
        ->and($empty->last(default: 'empty'))->toBe('empty')
        ->and($empty->firstKey())->toBeNull()->and($empty->lastKey())->toBeNull()
        ->and($empty->every(static fn(mixed $value): bool => false))->toBeTrue()
        ->and($empty->some(static fn(mixed $value): bool => true))->toBeFalse();
    $list = new ArrayList([0, false, null, '0']);
    expect($list->search(0))->toBe(0)->and($list->search('0'))->toBe(3)
        ->and($list->search('missing'))->toBeNull()
        ->and($list->contains(null))->toBeTrue()
        ->and($list->contains(static fn(mixed $value): bool => $value === null))->toBeTrue();
});

it('projects nested columns and selected fields without retaining an incompatible element type', function (): void {
    $list = new ArrayList([['name' => 'A', 'nested' => ['id' => 1]], ['name' => 'B', 'nested' => ['id' => 2]]]);
    expect($list->column('nested', 'id')->toArray())->toBe([1, 2])
        ->and($list->only(['name'])->toArray())->toBe([['name' => 'A'], ['name' => 'B']])
        ->and(new IntArrayList([1])->only(['unused'])->toArray())->toBe([1])
        ->and(new ArrayList([new ArrayList([1, 2]), [3]])->collapse()->toArray())->toBe([1, 2, 3]);
});

it('round trips mutable and immutable collections through PHP serialization', function (string $class, array $values): void {
    $list = new $class($values);
    $copy = unserialize(serialize($list));
    expect($copy)->toBeInstanceOf($class)->and($copy->toArray())->toBe($values)->and(iterator_to_array($copy))->toBe($values);
})->with([[ArrayList::class, [1, 'x', null]], [IntArrayList::class, [0, 2]], [StringArrayList::class, ['', '0']], [NumberArrayList::class, [1, 1.5]], [ReadonlyArrayList::class, ['a' => 1]]]);

it('rejects invalid replacement values and keeps the source collection intact', function (): void {
    $list = new IntArrayList([1, 2]);
    expect(fn(): IntArrayList => $list->merge(['bad']))->toThrow(CollectionInvalidTypeException::class)
        ->and(fn(): IntArrayList => $list->splice(0, 1, 'bad'))->toThrow(CollectionInvalidTypeException::class)
        ->and($list->toArray())->toBe([1, 2]);
});

it('caches null and false without rerunning a value factory', function (mixed $value): void {
    $map = new CacheMap();
    $count = 0;
    $factory = static function () use (&$count, $value): mixed {
        ++$count;

        return $value;
    };
    expect($map->remember('key', $factory))->toBe($value)
        ->and($map->remember('key', $factory))->toBe($value)
        ->and($map->has('key'))->toBeTrue()->and($count)->toBe(1);
})->with([[null], [false], [0], ['']]);

it('serializes maps and creates an independent immutable merge', function (): void {
    $map = new Map(['a' => null]);
    $map->set('b', false);
    $map->unset('missing');
    expect($map->has('a'))->toBeTrue()->and($map->get('b'))->toBeFalse()
        ->and(unserialize(serialize($map))->toArray())->toBe(['a' => null, 'b' => false]);
    $readOnly = new ReadonlyMap(['a' => 1]);
    $merged = $readOnly->merge(['b' => 2]);
    expect($merged->toArray())->toBe(['a' => 1, 'b' => 2])->and($readOnly->toArray())->toBe(['a' => 1]);
});

it('exports the pagination wire format', function (): void {
    $page = new Paginated(new ArrayList(['item']), 2, 10, 3, 25);
    expect(json_decode(json_encode($page), true))->toBe(['items' => ['item'], 'page' => 2, 'page_size' => 10, 'pages' => 3, 'total' => 25]);
});
