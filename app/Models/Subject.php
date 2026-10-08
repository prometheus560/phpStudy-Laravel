<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Subject extends Model
{
    protected $table = 'subjects';

    public $timestamps = false;

    // The categories a subject can have
    public const CATEGORIES = ['General', 'Major', 'Minor', 'Elective', 'PE', 'Other'];

    protected $fillable = [
        'user_id',
        'subject_name',
        'category',
    ];

   
    // The pages check this, so the site keeps working before and after the column is added.
    public static function hasCategory(): bool
    {
        static $has = null;

        return $has ??= Schema::hasColumn('subjects', 'category');
    }

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