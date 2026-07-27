<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log a user activity into the database.
     *
     * @param string $action (e.g. 'created', 'updated', 'deleted')
     * @param string $module (e.g. 'Sector', 'User', 'Role')
     * @param string $description (Human readable activity description)
     * @param mixed $subject (Model or null)
     * @param array $properties (Additional context metadata)
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        mixed $subject = null,
        array $properties = []
    ): ActivityLog {
        $subjectType = null;
        $subjectId = null;

        if (is_object($subject)) {
            $subjectType = get_class($subject);
            $subjectId = method_exists($subject, 'getKey') ? $subject->getKey() : ($subject->id ?? null);
        }

        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'properties' => $properties ?: null,
        ]);
    }
}
