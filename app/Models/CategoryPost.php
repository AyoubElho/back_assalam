<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryPost extends Model
{
    protected $table = 'category_posts'; // Explicit if needed
    protected $fillable = ['name']; // Add other fillable fields


    function posts()
    {
        return $this->hasMany(Post::class, 'category_id');
    }
}
