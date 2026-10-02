<?php
declare(strict_types=1);

use Raxos\Collection\{ArrayList, Paginated};

covers(Paginated::class);

it('preserves the item collection and exports empty and nonempty page metadata', function (array $items, int $page, int $pages, int $total): void {
    $list = new ArrayList($items);
    $result = new Paginated($list, $page, 10, $pages, $total);
    expect($result->items)->toBe($list)
        ->and(json_decode(json_encode($result, JSON_THROW_ON_ERROR), true))->toBe(['items' => $items, 'page' => $page, 'page_size' => 10, 'pages' => $pages, 'total' => $total]);
})->with([[[], 1, 0, 0], [['unit'], 2, 3, 21]]);
