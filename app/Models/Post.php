<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'created_by' // or 'created_by' depending on your schema
    ];

    // Add your relationships here
    public function images()
    {
        return $this->hasMany(PostImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(CategoryPost::class); // or CategoryPost::class if you're using that
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id'); // or CategoryPost::class if you're using that
    }
}
