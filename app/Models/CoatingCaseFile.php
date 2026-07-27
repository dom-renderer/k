<?php

namespace App\Models;

use App\Traits\UserTrackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CoatingCaseFile extends Model
{
    use HasFactory, UserTrackable;

    protected $fillable = [
        'coating_case_id',
        'level',
        'file_category',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'added_by',
    ];

    protected $casts = [
        'level' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Relationship to CoatingCase.
     */
    public function coatingCase(): BelongsTo
    {
        return $this->belongsTo(CoatingCase::class, 'coating_case_id');
    }

    /**
     * Get full public URL for the file.
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    /**
     * Helper to check if file is an image.
     */
    public function getIsImageAttribute(): bool
    {
        return str_contains($this->file_type ?? '', 'image') || in_array(strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }
}
