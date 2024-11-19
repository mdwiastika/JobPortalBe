<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedJob extends Model
{
    protected $fillable = [
        'job_seeker_id',
        'job_posting_id',
    ];

    public function jobSeeker()
    {
        return $this->belongsTo(JobSeeker::class, 'job_seeker_id', 'user_id');
    }

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }
}
