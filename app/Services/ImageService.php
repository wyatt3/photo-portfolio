<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
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
        
        $originalPath = 'images/original/' . $filename;
        $watermarkPath = 'images/watermark/' . $filename;
        $thumbnailPath = 'images/thumbnail/' . $filename;

        $image = $this->imageManager->decodePath($file->getPathname());
        $width = $image->width();
        $height = $image->height();

        Storage::disk('public')->put($originalPath, $file->get());

        $watermarked = $this->applyTiledWatermark(clone $image);
        Storage::disk('public')->put($watermarkPath, $watermarked->encodeUsingFormat(Format::JPEG, 85)->toString());

        $thumbnail = $this->generateThumbnail($image);
        Storage::disk('public')->put($thumbnailPath, $thumbnail->encodeUsingFormat(Format::JPEG, 80)->toString());

        return $album->images()->create([
            'original_path' => $originalPath,
            'watermark_path' => $watermarkPath,
            'thumbnail_path' => $thumbnailPath,
            'width' => $width,
            'height' => $height,
        ]);
    }

    public function delete(Image $image): void
    {
        Storage::disk('public')->delete([
            $image->original_path,
            $image->watermark_path,
            $image->thumbnail_path,
        ]);

        $image->delete();
    }

    private function applyTiledWatermark($image)
    {
        $fontSize = max(48, (int) ($image->width() / 5));
        $opacity = 0.15;

        $textWidth = strlen($this->watermarkText) * $fontSize * 0.6;
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
                    $this->watermarkText,
                    $offsetX,
                    $offsetY,
                    fn($font) => $font->size($fontSize)
                        ->color('ffffff', $opacity)
                        ->align('left', 'top')
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
            $image->cover($maxDimension, $maxDimension);
        }

        return $image;
    }
}
