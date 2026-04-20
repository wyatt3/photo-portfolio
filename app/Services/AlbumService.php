<?php

namespace App\Services;

use App\Models\Album;
use Illuminate\Support\Str;

class AlbumService
{
    public function create(array $data): Album
    {
        $data['slug'] = $this->generateUniqueSlug($data['title']);

        if (!empty($data['published_at'])) {
            $data['is_published'] = true;
        }

        return Album::create($data);
    }

    public function update(Album $album, array $data): Album
    {
        if (isset($data['title']) && $data['title'] !== $album->title) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $album->id);
        }

        if (array_key_exists('is_published', $data)) {
            $data['published_at'] = $data['is_published'] ? now() : null;
        }

        $album->update($data);
        return $album->fresh();
    }

    public function delete(Album $album): void
    {
        $album->delete();
    }

    public function setCover(Album $album, int $imageId): Album
    {
        $album->update(['cover_image_id' => $imageId]);
        return $album->fresh();
    }

    public function syncTags(Album $album, array $tagIds): Album
    {
        $album->tags()->sync($tagIds);
        return $album->fresh()->load('tags');
    }

    public function getPublished()
    {
        $query = Album::published()->with(['coverImage', 'tags']);

        return $query->orderByDesc('created_at')->get();
    }

    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 1;

        $query = Album::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $original . '-' . $counter++;
            $query = Album::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }
}
