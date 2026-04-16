<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagService
{
    public function create(array $data): Tag
    {
        $data['slug'] = $this->generateUniqueSlug($data['name']);
        return Tag::create($data);
    }

    public function update(Tag $tag, array $data): Tag
    {
        if (isset($data['name']) && $data['name'] !== $tag->name) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $tag->id);
        }

        $tag->update($data);
        return $tag->fresh();
    }

    public function delete(Tag $tag): void
    {
        $tag->delete();
    }

    public function findBySlug(string $slug): ?Tag
    {
        return Tag::where('slug', $slug)->first();
    }

    public function getAllWithAlbumCount(): \Illuminate\Database\Eloquent\Collection
    {
        return Tag::withCount('albums')
            ->orderBy('name')
            ->get();
    }

    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        $query = Tag::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $original . '-' . $counter++;
            $query = Tag::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }
}
