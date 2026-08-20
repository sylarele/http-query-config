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

## Validation des filtres

Les filtres valident automatiquement leur valeur en fonction de leur `FilterType`.
Ces règles peuvent être remplacées ou complétées depuis la Query, sans passer par la FormRequest :

```php
protected function configure(QueryConfig $config): void
{
    // Remplace les règles déduites du type, pour `state[value]`
    $config
        ->filter('state')
        ->type(FilterType::String)
        ->withValidation(['nullable', 'string', new Enum(FooState::class)]);

    // Ajoute des règles sur une sous-clé de la valeur, ici `states[value][*]`
    $config
        ->filter('states')
        ->field('state')
        ->type(FilterType::Array)
        ->addedValidation('*', ['required', 'string', new Enum(FooState::class)]);
}
```

Les règles des clés `mode` et `not` du filtre restent déduites du `FilterType`.