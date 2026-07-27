<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait UserTrackable
{
    /**
     * Boot the trait to set added_by and updated_by automatically.
     */
    protected static function bootUserTrackable(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && empty($model->added_by)) {
                $model->added_by = Auth::id();
            }
            if (Auth::check() && empty($model->updated_by)) {
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Get the user who added this model.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Get the user who last updated this model.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
