# HttpQueryConfig

## Introduction

Les Queries sont des classes définies dans le Domain (dossier Queries).
Il doit y avoir une Query par Model queryable. Les Queries sont utilisées pour simplifier la configuration de filtres, scopes, relations, etc, … et pour simplifier la pagination des résultats.

## Requirements

### PHP version

| Version PHP | HttpQueryConfig 0.x |
|-------------|---------------------|
| <= 8.2      | ✗ Unsupported       |
| 8.3         | ✓ Supported         |

## Exemple simple

```php
<?php

declare(strict_types=1);

namespace Domain\Acme\Queries;

use Sylarele\HttpQueryConfig\Query\Query;
use Sylarele\HttpQueryConfig\Query\QueryConfig;
use Sylarele\HttpQueryConfig\Models\MyModel;

class FooModelQuery extends Query
{
    protected function model(): string
    {
        return FooModel::class;
    }

    protected function configure(QueryConfig $config): void
    {
        $config->filter('filterName')->type(FilterType::Type);
    }
}
```