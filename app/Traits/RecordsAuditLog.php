<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait RecordsAuditLog
{
    public static function bootRecordsAuditLog()
    {
        static::created(function ($model) {
            self::recordAudit($model, 'created');
        });

        static::updated(function ($model) {
            self::recordAudit($model, 'updated');
        });

        static::deleted(function ($model) {
            self::recordAudit($model, 'deleted');
        });
    }

    protected static function recordAudit($model, $action)
    {
        $oldValues = [];
        $newValues = [];

        if ($action === 'updated') {
            $oldValues = array_intersect_key($model->getOriginal(), $model->getChanges());
            $newValues = $model->getChanges();
        } elseif ($action === 'created') {
            $newValues = $model->getAttributes();
        } elseif ($action === 'deleted') {
            $oldValues = $model->getAttributes();
        }

        // Don't record if no changes
        if ($action === 'updated' && empty($newValues)) {
            return;
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'table_name' => $model->getTable(),
            'record_id' => $model->getKey(),
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
