<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobSeekerSkill extends Model
{
    protected $fillable = [
        'job_seeker_id',
        'skill_id',
        'proficiency',
    ];

    public function jobSeeker()
    {
        return $this->belongsTo(JobSeeker::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }
}
