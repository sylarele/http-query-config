# HttpQueryConfig

[![License](https://img.shields.io/github/license/sylarele/http-query-config.svg)](https://github.com/sylarele/http-query-config/blob/main/LICENSE "LICENSE")
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/sylarele/http-query-config/php)
[![Packagist Downloads](https://img.shields.io/packagist/dm/sylarele/http-query-config)](https://packagist.org/packages/sylarele/http-query-config "Packagist")

Les Queries sont des classes définies dans le Domain (dossier Queries).
Il doit y avoir une Query par Model queryable. Les Queries sont utilisées pour simplifier la configuration de filtres, scopes, relations, etc, … et pour simplifier la pagination des résultats.

## Installation

```bash
composer require sylarele/http-query-config
```

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