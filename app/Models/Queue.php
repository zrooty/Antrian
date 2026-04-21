<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'schedule_id', 'service_id', 'counter_id', 'nomor_antrian', 'keluhan', 'status'])]
class Queue extends Model
{
    /** @use HasFactory<\Database\Factories\QueueFactory> */
    use HasFactory;

    /**
     * Queue Statuses
     */
    public const STATUS_WAITING = 'waiting';
    public const STATUS_CALLED = 'called';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DONE = 'done';
    public const STATUS_SKIPPED = 'skipped';

    /**
     * Get the user that owns the queue.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the counter that handled the queue.
     */
    public function counter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    /**
     * Get the service for the queue.
     */
    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the schedule that owns the queue.
     */
    public function schedule(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
    /**
     * Scopes
     */
    public function scopeWaiting($query) { return $query->where('status', 'waiting'); }
    public function scopeCalled($query) { return $query->where('status', 'called'); }
    public function scopeProcessing($query) { return $query->where('status', 'processing'); }
    public function scopeDone($query) { return $query->where('status', 'done'); }
    public function scopeSkipped($query) { return $query->where('status', 'skipped'); }

    /**
     * Helpers
     */
    public function isWaiting() { return $this->status === 'waiting'; }
    public function isCalled() { return $this->status === 'called'; }
    public function isProcessing() { return $this->status === 'processing'; }
}
