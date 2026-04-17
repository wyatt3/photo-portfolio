<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id',
        'original_path',
        'watermark_path',
        'thumbnail_path',
        'width',
        'height',
        'position',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function getUrl(string $type = 'watermark'): string
    {
        $path = match ($type) {
            'original' => $this->original_path,
            'thumbnail' => $this->thumbnail_path,
            default => $this->watermark_path,
        };

        return asset('storage/' . $path);
    }
}
