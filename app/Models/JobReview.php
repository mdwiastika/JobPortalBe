<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobReview extends Model
{
    protected $fillable = [
        'job_posting_id',
        'job_seeker_id',
        'rating',
        'review_text',
    ];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function jobSeeker()
    {
        return $this->belongsTo(JobSeeker::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'job_seeker_id', 'id');
    }
}
