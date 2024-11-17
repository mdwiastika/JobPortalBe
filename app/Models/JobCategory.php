<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    use Sluggable;
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
}
