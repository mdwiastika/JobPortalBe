<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPostingCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'job_posting_id',
        'job_category_id',
    ];

    public function jobPostings()
    {
        return $this->hasMany(JobPosting::class);
    }
    public function jobCategories()
    {
        return $this->hasMany(JobCategory::class);
    }
}
