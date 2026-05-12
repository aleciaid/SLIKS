<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->log('created', $model, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $original = collect($model->getOriginal())
            ->only(array_keys($changes))
            ->all();

        $this->log('updated', $model, $original, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->log($model->isForceDeleting() ? 'force_deleted' : 'deleted', $model, $model->getOriginal(), null);
    }

    public function restored(Model $model): void
    {
        $this->log('restored', $model, null, $model->getAttributes());
    }

    private function log(string $action, Model $model, ?array $oldValues, ?array $newValues): void
    {
        if ($model instanceof AuditLog) {
            return;
        }

        $user = auth()->user();
        $request = request();

        AuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'action' => $action,
            'model_type' => $model::class,
            'model_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
