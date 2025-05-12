<?php

namespace App\Models\widow_tempo;

use Illuminate\Database\Eloquent\Model;

class OrphanTempo extends Model
{
    protected $fillable = ['full_name', 'birth_date', 'widow_id','is_studying'];

    function widow()
    {
        return $this->belongsTo(WidowTempo::class);
    }
}
