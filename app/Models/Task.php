<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'task_name',
        'deadline',
        'priority',
        'status',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'Completed'
            && $this->deadline->copy()->startOfDay()->lt(now()->startOfDay());
    }

    public function getDueLabelAttribute(): string
    {
        if ($this->status === 'Completed') {
            return 'Done';
        }

        $days = (int) now()
            ->startOfDay()
            ->diffInDays($this->deadline->copy()->startOfDay(), false);

        if ($days < 0) {
            return 'Overdue by ' . abs($days) . 'd';
        }

        if ($days === 0) {
            return 'Due today';
        }

        return $days . ' day' . ($days > 1 ? 's' : '') . ' left';
    }
}