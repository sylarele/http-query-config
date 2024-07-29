<?php

declare(strict_types=1);

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Workbench\App\Http\Requests\IndexFooRequest;
use Workbench\App\Http\Resources\FooResource;
use Workbench\App\Models\Foo;

class FooController
{
    public function index(IndexFooRequest $request): JsonResponse
    {
        $foos = Foo::query()
            ->configureForQuery($request->toQuery())
            ->paginateForQuery($request->toQuery());

        return FooResource::collection($foos)->response();
    }
}
