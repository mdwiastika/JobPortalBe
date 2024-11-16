<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    use Sluggable;

    public function sluggable(): array
    {
        return [
            'slug_category' => [
                'source' => 'category_name'
            ]
        ];
    }
}
