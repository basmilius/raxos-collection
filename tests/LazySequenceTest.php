<?php
declare(strict_types=1);

use Raxos\Collection\LazySequence;
use Raxos\Error\InvalidArgumentException;

covers(LazySequence::class);

it('defers and short-circuits an unbounded factory while retaining keys', function (): void {
    $visited = 0;
    $sequence = LazySequence::from(function () use (&$visited): Generator {
        for ($i = 0; ; ++$i) {
            ++$visited;
            yield $i => $i;
        }
    })->filter(static fn(int $value): bool => $value % 2 === 0)->map(static fn(int $value): int => $value * 3)->take(3);
    expect($visited)->toBe(0)->and($sequence->toArray())->toBe([0 => 0, 2 => 6, 4 => 12])->and($visited)->toBe(5);
    expect($sequence->toArray(false))->toBe([0, 6, 12])->and($visited)->toBe(10);
});

it('chunks lazily and keeps the final incomplete chunk', function (): void {
    expect(LazySequence::from([1, 2, 3, 4, 5])->chunk(2)->toArray())->toBe([[1, 2], [3, 4], [5]])
        ->and(LazySequence::from([])->chunk(2)->toArray())->toBe([]);
    expect(fn() => LazySequence::from([])->chunk(0))->toThrow(InvalidArgumentException::class);
    expect(fn() => LazySequence::from([])->take(-1))->toThrow(InvalidArgumentException::class);
});

it('does not touch a one-shot iterator for take zero and rejects re-use', function (): void {
    $source = (static function (): Generator {
        yield 'name' => 'one';
    })();
    $sequence = LazySequence::from($source);
    expect($sequence->take(0)->toArray())->toBe([])->and($sequence->toArray())->toBe(['name' => 'one']);
    expect(fn() => $sequence->toArray())->toThrow(InvalidArgumentException::class);
});

it('passes values and keys through each and propagates factory and callback failures', function (): void {
    $values = [];
    LazySequence::from(['key' => 'value'])->each(function (string $value, string $key) use (&$values): void {
        $values[$key] = $value;
    });
    expect($values)->toBe(['key' => 'value']);
    expect(fn() => LazySequence::from(static fn() => 42)->toArray())->toThrow(InvalidArgumentException::class);
    expect(fn() => LazySequence::from([1])->map(static fn() => throw new LogicException('map'))->toArray())->toThrow(LogicException::class, 'map');
});

it('closes a factory generator when short-circuiting or propagating an exception', function (): void {
    $closed = 0;
    $sequence = LazySequence::from(static function () use (&$closed): Generator {
        try {
            for ($id = 0; ; ++$id) {
                yield $id;
            }
        } finally {
            ++$closed;
        }
    });
    expect($sequence->take(2)->toArray())->toBe([0, 1])->and($closed)->toBe(1);
    expect(fn() => $sequence->map(static fn(int $value) => throw new LogicException('stop'))->toArray())->toThrow(LogicException::class);
    expect($closed)->toBe(2);
});
