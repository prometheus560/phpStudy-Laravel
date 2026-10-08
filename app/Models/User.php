<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use Notifiable, HasPushSubscriptions;

    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    public function studyFiles()
    {
        return $this->hasMany(StudyFile::class, 'user_id');
    }
}