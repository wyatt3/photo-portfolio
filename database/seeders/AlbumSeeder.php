<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Image;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AlbumSeeder extends Seeder
{
    use WithoutModelEvents;

    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    public function run(): void
    {
        $this->createTags();
        $this->createAlbumWithImages('Nature', 'nature', [
            'https://picsum.photos/id/10/1200/800',
            'https://picsum.photos/id/11/1200/800',
            'https://picsum.photos/id/12/1200/800',
            'https://picsum.photos/id/13/1200/800',
            'https://picsum.photos/id/14/1200/800',
            'https://picsum.photos/id/15/1200/800',
            'https://picsum.photos/id/16/1200/800',
            'https://picsum.photos/id/17/1200/800',
        ], ['nature', 'landscape']);

        $this->createAlbumWithImages('Architecture', 'architecture', [
            'https://picsum.photos/id/20/1200/800',
            'https://picsum.photos/id/21/1200/800',
            'https://picsum.photos/id/22/1200/800',
            'https://picsum.photos/id/23/1200/800',
            'https://picsum.photos/id/24/1200/800',
            'https://picsum.photos/id/25/1200/800',
        ], ['architecture', 'urban']);

        $this->createAlbumWithImages('Ocean', 'ocean', [
            'https://picsum.photos/id/30/1200/800',
            'https://picsum.photos/id/31/1200/800',
            'https://picsum.photos/id/32/1200/800',
            'https://picsum.photos/id/33/1200/800',
            'https://picsum.photos/id/34/1200/800',
            'https://picsum.photos/id/35/1200/800',
            'https://picsum.photos/id/36/1200/800',
        ], ['ocean', 'nature']);

        $this->createAlbumWithImages('Mountains', 'mountains', [
            'https://picsum.photos/id/40/1200/800',
            'https://picsum.photos/id/41/1200/800',
            'https://picsum.photos/id/42/1200/800',
            'https://picsum.photos/id/43/1200/800',
            'https://picsum.photos/id/44/1200/800',
        ], ['mountains', 'nature', 'landscape']);

        $this->createAlbumWithImages('City Nights', 'city-nights', [
            'https://picsum.photos/id/50/1200/800',
            'https://picsum.photos/id/51/1200/800',
            'https://picsum.photos/id/52/1200/800',
            'https://picsum.photos/id/53/1200/800',
            'https://picsum.photos/id/54/1200/800',
            'https://picsum.photos/id/55/1200/800',
            'https://picsum.photos/id/56/1200/800',
            'https://picsum.photos/id/57/1200/800',
        ], ['urban', 'night']);
    }

    private function createTags(): void
    {
        $tags = ['nature', 'landscape', 'architecture', 'urban', 'ocean', 'mountains', 'night', 'portrait'];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['name' => ucfirst($tag), 'slug' => $tag]);
        }
    }

    private function createAlbumWithImages(string $title, string $slug, array $urls, array $tagSlugs): void
    {
        $album = Album::firstOrCreate(
            ['slug' => $slug],
            [
                'title' => $title,
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        $tagIds = Tag::whereIn('slug', $tagSlugs)->pluck('id');
        $album->tags()->sync($tagIds);

        $coverImage = null;

        foreach ($urls as $url) {
            $image = $this->downloadAndProcessImage($url, $album->id);
            if ($coverImage === null) {
                $coverImage = $image;
            }
        }

        if ($coverImage) {
            $album->update(['cover_image_id' => $coverImage->id]);
        }
    }

    private function downloadAndProcessImage(string $url, int $albumId): Image
    {
        $response = Http::get($url);
        if (!$response->successful()) {
            throw new \Exception("Failed to download image from: $url");
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'photo_');
        file_put_contents($tempFile, $response->body());

        $filename = uniqid() . '.jpg';

        $originalPath = 'images/original/' . $filename;
        $watermarkPath = 'images/watermark/' . $filename;
        $thumbnailPath = 'images/thumbnail/' . $filename;

        $image = $this->imageManager->decodePath($tempFile);
        $width = $image->width();
        $height = $image->height();

        Storage::disk('public')->put($originalPath, file_get_contents($tempFile));

        $watermarked = $this->applyTiledWatermark($image);
        $watermarked->save(Storage::disk('public')->path($watermarkPath), 85);

        $thumbnail = $this->generateThumbnail($image);
        $thumbnail->save(Storage::disk('public')->path($thumbnailPath), 80);

        unlink($tempFile);

        return Image::create([
            'album_id' => $albumId,
            'watermark_path' => $watermarkPath,
            'thumbnail_path' => $thumbnailPath,
            'width' => $width,
            'height' => $height,
        ]);
    }

    private function applyTiledWatermark($image): mixed
    {
        $watermarkText = config('app.name', 'Photography');
        $fontSize = max(16, (int) ($image->width() / 30));
        $opacity = 0.25;

        $textWidth = strlen($watermarkText) * $fontSize * 0.6;
        $textHeight = $fontSize * 1.5;

        $angle = -30;
        $spacingX = $textWidth * 1.5;
        $spacingY = $textHeight * 3;

        $cols = (int) ceil($image->width() / $spacingX) + 4;
        $rows = (int) ceil($image->height() / $spacingY) + 4;

        for ($row = -2; $row < $rows; $row++) {
            for ($col = -2; $col < $cols; $col++) {
                $offsetX = $col * $spacingX + ($row % 2 ? $spacingX / 2 : 0);
                $offsetY = $row * $spacingY;

                $image->text(
                    $watermarkText,
                    $offsetX,
                    $offsetY,
                    fn($font) => $font->size($fontSize)
                        ->color('ffffff', $opacity)
                        ->align('left')
                        ->angle($angle)
                );
            }
        }

        return $image;
    }

    private function generateThumbnail($image): mixed
    {
        $maxDimension = (int) config('photos.thumbnail_max_dimension', 800);

        if ($image->width() > $maxDimension || $image->height() > $maxDimension) {
            $image->resize(
                $image->width() > $image->height() ? $maxDimension : null,
                $image->height() >= $image->width() ? $maxDimension : null
            );
        }

        return $image;
    }
}
