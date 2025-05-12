<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = ['user_id', 'amount', 'session_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
