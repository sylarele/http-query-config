<?php

declare(strict_types=1);

namespace Workbench\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Override;
use Workbench\App\Models\Foo;

/**
 * @property Foo $resource
 */
class FooResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    #[Override]
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'size' => $this->resource->size,
            'state' => $this->resource->state->value,
            'bars' => $this->whenLoaded(
                'bars',
                fn () => BarResource::collection($this->resource->bars)
            )
        ];
    }
}
