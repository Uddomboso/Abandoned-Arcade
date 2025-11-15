<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'developer' => $this->developer,
            'publisher' => $this->publisher,
            'release_date' => $this->release_date?->format('Y-m-d'),
            'image_url' => $this->image_url,
            'game_url' => $this->game_url,
            'genre' => [
                'id' => $this->genre->id,
                'name' => $this->genre->name,
                'slug' => $this->genre->slug,
            ],
            'rating' => (float) $this->rating,
            'rating_count' => $this->rating_count,
            'play_count' => $this->play_count,
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
