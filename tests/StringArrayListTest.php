<?php
declare(strict_types=1);

use Raxos\Collection\Error\CollectionInvalidTypeException;
use Raxos\Collection\StringArrayList;

covers(StringArrayList::class);

it('accepts its element type and exposes its specialized operation', function (): void {
    $list = new StringArrayList(['', '0', 'unit']);
    expect($list->toArray())->toBe(['', '0', 'unit']);
    expect($list->join('|'))->toBe('|0|unit')->and(new StringArrayList(['a', 'b', 'c'])->commaCommaAnd())->toBe('a, b & c')->and(new StringArrayList()->join())->toBe('');
});

it('rejects invalid element types before any mutation takes effect', function (): void {
    foreach ([0, 1.0, null, false, [], new stdClass()] as $invalid) {
        $list = new StringArrayList(['', '0', 'unit']);
        expect(fn() => new StringArrayList([$invalid]))->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list->append($invalid))->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list->prepend($invalid))->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list[0] = $invalid)->toThrow(CollectionInvalidTypeException::class)
            ->and(fn() => $list->__unserialize([$invalid]))->toThrow(CollectionInvalidTypeException::class)
            ->and($list->toArray())->toBe(['', '0', 'unit']);
    }
});
