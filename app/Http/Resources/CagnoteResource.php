<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CagnoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $baseUrl = config('app.url');

        // Build absolute URL for image
        $imageUrl = $this->image_url ?? '';
        if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
            $imageUrl = $baseUrl . '/' . ltrim($imageUrl, '/');
        }

        // Build absolute URL for organizer logo
        $organizerLogoPath = '';
        if ($this->user && $this->user->logo_path) {
            $organizerLogoPath = $this->user->logo_path;
            if (!str_starts_with($organizerLogoPath, 'http')) {
                $organizerLogoPath = $baseUrl . '/' . ltrim($organizerLogoPath, '/');
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'location' => $this->location,
            'city' => $this->city,
            'objective_amount' => (float)$this->objective_amount,
            'collected_amount' => (float)$this->collected_amount,
            'start_date' => $this->start_date,
            'deadline' => $this->deadline,
            'image_url' => $imageUrl,
            'photos' => $this->photos ?? [],
            'organizer' => $this->user?->name ?? 'Unknown',
            'organizer_logo_path' => $organizerLogoPath,
            'publication_status' => $this->publication_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
