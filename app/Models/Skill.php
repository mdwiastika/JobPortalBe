<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    protected $fillable = [
        'skill_name',
    ];
    public function jobPostings()
    {
        return $this->belongsToMany(JobPosting::class, 'job_posting_skills', 'skill_id', 'job_posting_id');
    }
    public function jobSeekers()
    {
        return $this->belongsToMany(JobSeeker::class, 'job_seeker_skills', 'skill_id', 'job_seeker_id');
    }
}
