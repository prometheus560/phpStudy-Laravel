<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyFile extends Model
{
    protected $table = 'study_files';

    protected $fillable = [
        'user_id',
        'subject_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}