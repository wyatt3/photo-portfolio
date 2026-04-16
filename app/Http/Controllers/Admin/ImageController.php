<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ImageController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function index(Album $album)
    {
        $album->load(['images', 'tags']);

        return Inertia::render('Admin/Albums/Images', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
                'description' => $album->description,
                'cover_image_id' => $album->cover_image_id,
                'images' => $album->images->map(fn($image) => [
                    'id' => $image->id,
                    'thumbnail_url' => $image->getUrl('thumbnail'),
                    'watermark_url' => $image->getUrl('watermark'),
                    'width' => $image->width,
                    'height' => $image->height,
                ]),
            ],
        ]);
    }

    public function store(Request $request, Album $album)
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:10240'],
        ]);

        $images = [];
        foreach ($request->file('images') as $file) {
            $images[] = $this->imageService->upload($album, $file);
        }

        $message = count($images) === 1 
            ? 'Image uploaded successfully.' 
            : count($images) . ' images uploaded successfully.';

        return back()->with('success', $message);
    }

    public function destroy(Image $image)
    {
        $album = $image->album;
        $this->imageService->delete($image);

        if ($album->cover_image_id === $image->id) {
            $album->update(['cover_image_id' => null]);
        }

        return back()->with('success', 'Image deleted.');
    }

    public function setCover(Album $album, Image $image)
    {
        abort_unless($image->album_id === $album->id, 404);
        $album->update(['cover_image_id' => $image->id]);
        return back()->with('success', 'Cover image updated.');
    }
}
