<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Tag;
use App\Services\AlbumService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AlbumController extends Controller
{
    public function __construct(private AlbumService $albumService) {}

    public function index()
    {
        $albums = Album::with(['coverImage', 'tags'])
            ->withCount('images')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Admin/Albums/Index', [
            'albums' => $albums,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Albums/Create', [
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_published' => ['boolean'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['exists:tags,id'],
        ]);

        $album = $this->albumService->create($validated);

        if (!empty($validated['tag_ids'])) {
            $this->albumService->syncTags($album, $validated['tag_ids']);
        }

        return redirect("/admin/albums/{$album->id}/images")
            ->with('success', 'Album created successfully.');
    }

    public function edit(Album $album)
    {
        $album->load(['tags', 'images']);

        return Inertia::render('Admin/Albums/Edit', [
            'album' => $album,
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Album $album)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_published' => ['boolean'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['exists:tags,id'],
            'cover_image_id' => ['nullable', 'exists:images,id'],
        ]);

        $album = $this->albumService->update($album, $validated);

        if (array_key_exists('tag_ids', $validated)) {
            $this->albumService->syncTags($album, $validated['tag_ids'] ?? []);
        }

        return back()->with('success', 'Album updated.');
    }

    public function destroy(Album $album)
    {
        $this->albumService->delete($album);
        return redirect('/admin/albums')->with('success', 'Album deleted.');
    }
}
