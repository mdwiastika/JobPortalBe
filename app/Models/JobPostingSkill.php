<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPostingSkill extends Model
{
    use HasFactory;
    protected $fillable = [
        'job_posting_id',
        'skill_id',
    ];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }
}
