<?php
declare(strict_types=1);

use Raxos\Collection\{ArrayList, ArrayListAccessible, ReadonlyArrayList};
use Raxos\Collection\Error\CollectionImmutableException;

covers(ArrayListAccessible::class);

it('supports indexed assignment, append, unset, iteration and JSON export', function (): void {
    $list = new ArrayList(['zero' => 0, 'null' => null]);
    $list['zero'] = false;
    $list[] = 'append';
    unset($list['null']);
    expect(isset($list['zero']))->toBeTrue()->and(isset($list['missing']))->toBeFalse()
        ->and($list['zero'])->toBeFalse()->and(count($list))->toBe(2)
        ->and(iterator_to_array($list))->toBe(['zero' => false, 0 => 'append'])
        ->and($list->jsonSerialize())->toBe($list->toArray());
});

it('rejects every array mutation on immutable lists', function (string $operation): void {
    $list = new ReadonlyArrayList([1]);
    expect(function () use ($list, $operation): void {
        if ($operation === 'unset') {
            unset($list[0]);
        } else {
            $list[0] = 2;
        }
    })->toThrow(CollectionImmutableException::class);
    expect($list->toArray())->toBe([1]);
})->with(['unset', 'replace']);
