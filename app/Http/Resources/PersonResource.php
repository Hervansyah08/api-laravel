<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'education' => $this->education,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'created_at_format' => $this->created_at->isoFormat('D MMM Y'),
        ];
    }
}
