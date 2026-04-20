<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['tanggal', 'kuota_maksimal'])]
class Schedule extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleFactory> */
    use HasFactory;

    /**
     * Get the queues for the schedule.
     */
    public function queues(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Queue::class);
    }
}
