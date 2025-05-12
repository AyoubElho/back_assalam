<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReaction extends Model
{
    // The table associated with the model (optional if following Laravel's naming conventions)
    protected $table = 'post_reactions';

    // Define the columns that can be mass-assigned
    protected $fillable = [
        'user_id',
        'post_id',
        'reaction',
    ];

    // Relationships
    // A PostReaction belongs to a User (the user who reacted)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A PostReaction belongs to a Post (the post that was reacted to)
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
