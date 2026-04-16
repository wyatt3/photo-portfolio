<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AlbumController extends Controller
{
    public function show(string $slug)
    {
        $album = Album::where('slug', $slug)
            ->published()
            ->with(['images', 'tags'])
            ->firstOrFail();

        return Inertia::render('Album', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
                'description' => $album->description,
                'published_at' => $album->published_at?->toIso8601String(),
                'tags' => $album->tags->map(fn($tag) => [
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ]),
                'images' => $album->images->map(fn($image) => [
                    'id' => $image->id,
                    'thumbnail_url' => $image->getUrl('thumbnail'),
                    'full_url' => $image->getUrl('watermark'),
                    'width' => $image->width,
                    'height' => $image->height,
                ]),
            ],
        ]);
    }
}
