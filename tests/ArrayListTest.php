<?php
declare(strict_types=1);

use Raxos\Collection\{ArrayList};

covers(ArrayList::class);

it('mutates both ends of a list and returns removed values', function (): void {
    $list = new ArrayList([2]);
    $list->prepend(1);
    $list->append(3);
    expect($list->shift())->toBe(1)->and($list->pop())->toBe(3)->and($list->toArray())->toBe([2])
        ->and($list->pop())->toBe(2)->and($list->pop())->toBeNull()->and($list->shift())->toBeNull();
});

it('creates independent lists from arrays, lists and keyed iterators', function (): void {
    $source = new ArrayList(['a' => 1, 'b' => 2]);
    foreach ([$source, $source->toArray(), new ArrayIterator($source->toArray())] as $input) {
        $copy = ArrayList::of($input);
        expect($copy)->not->toBe($source)->and($copy->toArray())->toBe(['a' => 1, 'b' => 2]);
        $copy->append(3);
    }
    expect($source->__debugInfo())->toBe(['a' => 1, 'b' => 2]);
});
