<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Color;
use Intervention\Image\Format;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    private ImageManager $imageManager;
    private string $watermarkText;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
        $this->watermarkText = config('app.name', 'Photography');
    }

    public function upload(Album $album, UploadedFile $file): Image
    {
        $filename = uniqid() . '.jpg';

        $watermarkPath = 'images/watermark/' . $filename;
        $thumbnailPath = 'images/thumbnail/' . $filename;

        $image = $this->imageManager->decodePath($file->getPathname());
        $width = $image->width();
        $height = $image->height();

        $watermarked = $this->applyTiledWatermark(clone $image);
        Storage::disk('public')->put($watermarkPath, $watermarked->encodeUsingFormat(Format::JPEG, 85)->toString());

        $thumbnail = $this->generateThumbnail($image);
        Storage::disk('public')->put($thumbnailPath, $thumbnail->encodeUsingFormat(Format::JPEG, 80)->toString());

        return $album->images()->create([
            'watermark_path' => $watermarkPath,
            'thumbnail_path' => $thumbnailPath,
            'width' => $width,
            'height' => $height,
        ]);
    }

    public function delete(Image $image): void
    {
        Storage::disk('public')->delete([
            $image->watermark_path,
            $image->thumbnail_path,
        ]);

        $image->delete();
    }


    private function applyTiledWatermark($image)
    {
        $fontSizeRatio = (float) config('photos.watermark_font_size_ratio', 0.02);
        $watermarkOpacity = (int) config('photos.watermark_opacity', 30);
        $spacingRatio = (float) config('photos.watermark_spacing_ratio', 0.30);
        $watermarkFont = config('photos.watermark_font', storage_path('app/fonts/Inter.ttf'));

        $watermarkText = $this->watermarkText;
        $watermarkColor = Color::rgb(255, 255, 255, $watermarkOpacity / 100);

        $imageWidth = $image->width();
        $imageHeight = $image->height();

        $minDimension = min($imageWidth, $imageHeight);
        $watermarkSize = (int) round($minDimension * $fontSizeRatio);
        if ($watermarkSize < 14) {
            $watermarkSize = 14;
        }
        $spacing = (int) round($minDimension * $spacingRatio);
        if ($spacing < 80) {
            $spacing = 80;
        }

        for ($y = 0; $y < $imageHeight; $y += $spacing) {
            for ($x = 0; $x < $imageWidth; $x += $spacing) {
                $offsetX = ($y / $spacing % 2) * ($spacing / 2);
                $image->text($watermarkText, $x + $offsetX + $spacing / 2, $y + $spacing / 2, function ($font) use ($watermarkSize, $watermarkColor, $watermarkFont) {
                    $font->size($watermarkSize);
                    $font->color($watermarkColor);
                    $font->filepath($watermarkFont);
                    $font->align('center');
                });
            }
        }

        return $image;
    }

    private function generateThumbnail($image): mixed
    {
        $maxDimension = (int) config('photos.thumbnail_max_dimension', 800);

        if ($image->width() > $maxDimension || $image->height() > $maxDimension) {
            $image->cover($maxDimension, $maxDimension);
        }

        return $image;
    }
}
