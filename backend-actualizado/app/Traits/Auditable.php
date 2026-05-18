<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAudit('create', $model);
        });

        static::updated(function ($model) {
            self::logAudit('update', $model);
        });

        static::deleted(function ($model) {
            self::logAudit('delete', $model);
        });
    }

    protected static function logAudit($action, $model)
    {
        // En nuestro middleware, agregamos 'auth_user_id' a la petición
        $userId = request()->get('auth_user_id') ?? null;
        
        // Obtener valores antiguos y nuevos para el log
        $oldValues = null;
        $newValues = null;

        if ($action === 'update') {
            $changes = $model->getChanges();
            $oldValues = json_encode(array_intersect_key($model->getOriginal(), $changes));
            $newValues = json_encode($changes);
        } elseif ($action === 'create') {
            $newValues = json_encode($model->getAttributes());
        } elseif ($action === 'delete') {
            $oldValues = json_encode($model->getOriginal());
        }

        DB::table('audit_logs')->insert([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
