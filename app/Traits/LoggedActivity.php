<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LoggedActivity
{
    protected static function bootLoggedActivity()
    {
        foreach (static::getLoggedEvents() as $event) {
            static::$event(function ($model) use ($event) {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => strtoupper($event) . '_' . strtoupper(class_basename($model)),
                    'description' => "User " . (Auth::user()->name ?? 'System') . " {$event} " . class_basename($model) . " (ID: {$model->id})",
                    'metadata' => $model->getAttributes(),
                ]);
            });
        }
    }

    protected static function getLoggedEvents(): array
    {
        return ['created', 'updated', 'deleted'];
    }
}
