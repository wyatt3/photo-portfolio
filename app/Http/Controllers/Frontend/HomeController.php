<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\AlbumService;

class HomeController extends Controller
{
    public function __construct(private AlbumService $albumService) {}

    public function index()
    {
        $albums = $this->albumService->getPublished();
        $tags = Tag::withCount('albums')->orderBy('name')->get();
        return inertia('Home', [
            'albums' => $albums->map(fn($album) => [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
                'description' => $album->description,
                'published_at' => $album->published_at?->toIso8601String(),
                'cover_url' => $album->coverImage?->getUrl('thumbnail'),
                'image_count' => $album->images->count(),
                'tags' => $album->tags->map(fn($tag) => [
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ]),
            ]),
            'tags' => $tags->map(fn($tag) => [
                'name' => $tag->name,
                'slug' => $tag->slug,
                'album_count' => $tag->albums_count,
            ]),
        ]);
    }
}
