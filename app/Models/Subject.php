<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'subject_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'subject_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'subject_id');
    }

    public function notes()
{
    return $this->hasMany(Note::class, 'subject_id');
}
public function studyFiles()
{
    return $this->hasMany(StudyFile::class, 'subject_id');
}
}