<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use Sluggable;
    protected $fillable = [
        'recruiter_id',
        'title',
        'slug',
        'description',
        'requirements',
        'employment_type',
        'experience_level',
        'work_type',
        'min_salary',
        'max_salary',
        'location',
        'is_disability',
    ];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
    public function recruiter()
    {
        return $this->belongsTo(Recruiter::class, 'user_id', 'recruiter_id');
    }
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_posting_skills', 'job_posting_id', 'skill_id');
    }
    public function jobCategories()
    {
        return $this->belongsToMany(JobCategory::class, 'job_posting_categories', 'job_posting_id', 'job_category_id');
    }
}
