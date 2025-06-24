<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'author',
        'isbn',
        'pages',
        'language',
        'publication_year',
        'publisher',
        'cover_image',
        'pdf_file',
        'file_size',
        'download_count',
        'view_count',
        'rating',
        'is_featured',
        'is_active',
        'category_id'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
        'publication_year' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getPdfUrl()
    {
        return Storage::url($this->pdf_file);
    }

    public function getCoverImageUrl()
    {
        return $this->cover_image ? Storage::url($this->cover_image) : null;
    }

    public function getFileSizeFormatted()
    {
        if (!$this->file_size) return 'غير محدد';

        $size = $this->file_size;
        $units = ['بايت', 'كيلوبايت', 'ميجابايت', 'جيجابايت'];
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}
