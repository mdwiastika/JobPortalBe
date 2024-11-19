<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    use Sluggable, HasFactory;
    protected $fillable = [
        'category_name',
        'slug_category',
        'icon',
    ];
    public function sluggable(): array
    {
        return [
            'slug_category' => [
                'source' => 'category_name'
            ]
        ];
    }
    public function jobPostings()
    {
        return $this->belongsToMany(JobPosting::class, 'job_posting_categories', 'job_category_id', 'job_posting_id');
    }
}
