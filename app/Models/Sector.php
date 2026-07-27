<?php

namespace App\Models;

use App\Traits\UserTrackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    use HasFactory, UserTrackable;

    protected $fillable = [
        'title',
        'description',
        'added_by',
        'updated_by',
    ];

    /**
     * Relationship to CoatingCases.
     */
    public function cases(): HasMany
    {
        return $this->hasMany(CoatingCase::class, 'sector_id');
    }
}
