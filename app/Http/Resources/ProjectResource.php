<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ulid,
            'number' => $this->project_number,
            'title' => $this->title,
            'status' => $this->status?->value,
            'wedding_date' => optional($this->wedding_date)?->toDateString(),
            'total' => $this->total_amount,
            'balance' => $this->balance(),
        ];
    }
}
