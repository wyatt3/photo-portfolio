<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function store(Request $request, Album $album)
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg'],
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

    public function reorder(Request $request, Album $album)
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer'],
        ]);

        foreach ($request->order as $position => $imageId) {
            Image::where('id', $imageId)
                ->where('album_id', $album->id)
                ->update(['position' => $position]);
        }

        return back()->with('success', 'Images reordered.');
    }
}
