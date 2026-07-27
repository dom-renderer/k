<?php

namespace App\Models;

use App\Traits\UserTrackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoatingCase extends Model
{
    use HasFactory, UserTrackable;

    protected $fillable = [
        'case_number',
        'oa_number',
        'sector_id',
        'equipment_id',
        'other_information',
        'current_level',
        'status',
        'closed_at',
        'closed_by',
        'added_by',
        'updated_by',
    ];

    protected $casts = [
        'current_level' => 'integer',
        'closed_at' => 'datetime',
    ];

    /**
     * Relationship to Sector.
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    /**
     * Relationship to Equipment.
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    /**
     * Relationship to Case Files.
     */
    public function files(): HasMany
    {
        return $this->hasMany(CoatingCaseFile::class, 'coating_case_id');
    }

    /**
     * Relationship to Case Level Logs.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(CoatingCaseLevelLog::class, 'coating_case_id')->orderBy('created_at', 'desc');
    }

    /**
     * Relationship to User who closed the case.
     */
    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Helper to check if case is closed.
     */
    public function getIsClosedAttribute(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Generate HTML status badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        switch ($this->status) {
            case 'closed':
                return '<span class="badge bg-success"><i class="ti ti-lock me-1"></i>Closed</span>';
            case 'level_1_pending':
                return '<span class="badge bg-warning text-dark"><i class="ti ti-clock me-1"></i>Level 1 Pending</span>';
            case 'level_1_rejected':
                return '<span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>Level 1 Rejected</span>';
            case 'level_2_pending':
                return '<span class="badge bg-warning text-dark"><i class="ti ti-clock me-1"></i>Level 2 Pending</span>';
            case 'level_2_rejected':
                return '<span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>Level 2 Rejected</span>';
            case 'level_3_pending':
                return '<span class="badge bg-warning text-dark"><i class="ti ti-clock me-1"></i>Level 3 Pending</span>';
            case 'level_3_rejected':
                return '<span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>Level 3 Rejected</span>';
            default:
                return '<span class="badge bg-secondary">' . e(ucfirst($this->status)) . '</span>';
        }
    }
}
