<?php

namespace App\Models\widow;

use Illuminate\Database\Eloquent\Model;

class Orphan extends Model
{
    protected $fillable = [
        'full_name',
        'birth_date',
        'widow_id',
        'is_studying'
    ];

    function widow()
    {
        return $this->belongsTo(Widow::class, 'widow_id');
    }
}
