<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoatingCaseLevelLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'coating_case_id',
        'level',
        'action',
        'reset_to_level',
        'remarks',
        'user_id',
    ];

    protected $casts = [
        'level' => 'integer',
        'reset_to_level' => 'integer',
    ];

    /**
     * Relationship to CoatingCase.
     */
    public function coatingCase(): BelongsTo
    {
        return $this->belongsTo(CoatingCase::class, 'coating_case_id');
    }

    /**
     * Relationship to Reviewer User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
