<?php

declare(strict_types=1);

namespace Workbench\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Override;
use Workbench\App\Models\Bar;

/**
 * @property Bar $resource
 */
class BarResource extends JsonResource
{
    /**
     * @return array<string,scalar>
     */
    #[Override]
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
