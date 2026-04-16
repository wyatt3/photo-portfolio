<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TagController extends Controller
{
    public function __construct(private TagService $tagService) {}

    public function index()
    {
        return Inertia::render('Admin/Tags/Index', [
            'tags' => $this->tagService->getAllWithAlbumCount(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tags,name'],
        ]);

        $this->tagService->create($validated);

        return back()->with('success', 'Tag created.');
    }

    public function destroy(Tag $tag)
    {
        $this->tagService->delete($tag);
        return back()->with('success', 'Tag deleted.');
    }
}
