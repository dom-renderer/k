<?php

namespace App\Models;

use App\Traits\UserTrackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Equipment extends Model
{
    use HasFactory, UserTrackable;

    protected $table = 'equipment';

    protected $fillable = [
        'name',
        'sku',
        'photo',
        'description',
        'added_by',
        'updated_by',
    ];

    /**
     * Get full public URL for the equipment photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return asset('storage/' . $this->photo);
        }

        return null;
    }
}
