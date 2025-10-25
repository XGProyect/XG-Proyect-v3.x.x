<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllianceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->alliance_id,
            'name' => $this->alliance_name,
            'tag' => $this->alliance_tag,
            'description' => $this->alliance_description,
            'web' => $this->alliance_web,
            'image' => $this->alliance_image,
            'accepting_applications' => $this->acceptsApplications(),
            'member_count' => $this->whenLoaded('members', fn() => $this->members->count()),
            'owner' => new UserResource($this->whenLoaded('owner')),
            'members' => UserResource::collection($this->whenLoaded('members')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
