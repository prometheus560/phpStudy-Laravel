<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyPreference extends Model
{
    protected $table = 'study_preferences';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'session_length',
        'peak',
        'windows',
        'commitments',
    ];

    protected $casts = [
        'windows'     => 'array',
        'commitments' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}