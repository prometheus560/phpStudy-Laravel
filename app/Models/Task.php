<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    public $timestamps = false;

    protected $fillable = [
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
}