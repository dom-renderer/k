<?php

namespace App\Models;

use App\Traits\UserTrackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory, UserTrackable;

    protected $fillable = [
        'title',
        'description',
        'added_by',
        'updated_by',
    ];
}
