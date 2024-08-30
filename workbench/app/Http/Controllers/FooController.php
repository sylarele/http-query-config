<?php

declare(strict_types=1);

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Workbench\App\Http\Requests\IndexFooRequest;
use Workbench\App\Http\Resources\FooResource;
use Workbench\App\Http\Services\FooService;

class FooController
{
    public function __construct(private readonly FooService $fooService)
    {
    }

    public function index(IndexFooRequest $request): JsonResponse
    {
        $list = $this->fooService->list($request->toQuery());

        return FooResource::collection($list)->response();
    }
}
