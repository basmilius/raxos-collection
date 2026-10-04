<a href="https://bas.dev">
    <img src="https://bmcdn.nl/assets/branding/logo.svg" alt="Bas Milius" height="48" />
</a>

---

# Raxos Collection

Ordered lists, keyed maps and pagination objects with iterable, array-access and JSON support.

[Documentation](https://raxos.dev/collection/) | [Packagist](https://packagist.org/packages/raxos/collection) | [Raxos](https://github.com/basmilius/raxos)

- Mutable and read-only lists and maps.
- Validated string, integer and number lists.
- Filtering, projections, grouping, sorting, slicing and reductions.
- Paginated results with item and page metadata.

## Installation

Requires PHP 8.5 or later. Composer checks the remaining package and extension dependencies declared in [composer.json](composer.json).

```sh
composer require "raxos/collection:^3.3"
```

## Usage

```php
<?php
declare(strict_types=1);

use Raxos\Collection\ArrayList;

require __DIR__ . '/vendor/autoload.php';

$products = ArrayList::of([
    ['name' => 'Keyboard', 'stock' => 5],
    ['name' => 'Mouse', 'stock' => 0],
]);

$names = $products
    ->filter(static fn(array $product): bool => $product['stock'] > 0)
    ->map(static fn(array $product): string => $product['name']);

echo json_encode($names);
```

Transformations leave the original collection intact. In 3.2, projections such as `map()`, `column()` and `keys()` return generic list interfaces rather than the original typed list. Read-only inputs keep read-only results.

## Documentation

- [Array lists](https://raxos.dev/collection/array-lists)
- [Typed lists](https://raxos.dev/collection/typed-lists)
- [Maps](https://raxos.dev/collection/maps)
- [Pagination](https://raxos.dev/collection/pagination)

## Testing

Run this library's Pest suite from the Raxos workspace:

```sh
git clone --recurse-submodules https://github.com/basmilius/raxos.git
cd raxos
composer install
vendor/bin/pest --testsuite=collection
```

See [Testing Raxos](https://github.com/basmilius/raxos/blob/main/TESTING.md) for PHP extensions, integration services and coverage commands. The library's [Tests workflow](.github/workflows/tests.yml) also runs in GitHub Actions.

## License

[MIT](LICENSE). Copyright (c) 2017 - present Bas Milius.

See [lazy sequences and cursor pages](https://raxos.dev/collection/lazy-sequences) for the optional APIs and their lifetime or transport guarantees.
