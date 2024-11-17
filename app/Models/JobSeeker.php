<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobSeeker extends Model
{
    protected $fillable = [
        'user_id',
        'resume',
        'phone',
        'location',
        'linkedin',
        'experience_level',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_seeker_skills', 'job_seeker_id', 'skill_id');
    }
}
