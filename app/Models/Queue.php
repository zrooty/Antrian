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
     * Appended Accessors
     */
    protected $appends = ['status_label', 'status_color', 'accent_color'];

    /**
     * Accessors
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_WAITING => 'MENUNGGU PANGGILAN',
            self::STATUS_CALLED => 'NOMOR DIPANGGIL',
            self::STATUS_PROCESSING => 'SEDANG DILAYANI',
            self::STATUS_DONE => 'SELESAI',
            self::STATUS_SKIPPED => 'TERLEWAT',
        ];

        return $labels[$this->status] ?? strtoupper($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            self::STATUS_WAITING => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800',
            self::STATUS_CALLED => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 border-blue-200 dark:border-blue-800 animate-pulse',
            self::STATUS_PROCESSING => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
            self::STATUS_DONE => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800',
            self::STATUS_SKIPPED => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 border-red-200 dark:border-red-800',
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600';
    }

    public function getAccentColorAttribute(): string
    {
        $colors = [
            self::STATUS_WAITING => 'bg-indigo-500',
            self::STATUS_CALLED => 'bg-blue-500',
            self::STATUS_PROCESSING => 'bg-indigo-500',
            self::STATUS_DONE => 'bg-green-500',
            self::STATUS_SKIPPED => 'bg-red-500',
        ];

        return $colors[$this->status] ?? 'bg-indigo-500';
    }

    /**
     * Helpers
     */
    public function isWaiting() { return $this->status === self::STATUS_WAITING; }
    public function isCalled() { return $this->status === self::STATUS_CALLED; }
    public function isProcessing() { return $this->status === self::STATUS_PROCESSING; }
    public function isDone() { return $this->status === self::STATUS_DONE; }
    public function isSkipped() { return $this->status === self::STATUS_SKIPPED; }
}
