<?php

namespace App\Models\widow_tempo;

use App\Models\RequestModel;
use Illuminate\Database\Eloquent\Model;

class WidowTempo extends Model
{
    protected $fillable = ['name', 'tel', 'cin', 'request_id','birth_date'];

    function request()
    {
        return $this->belongsTo(RequestModel::class, 'request_id');
    }

    function orphans()
    {
        return $this->hasMany(OrphanTempo::class, 'widow_id'); // Assuming orphan has a widow_id field
    }

}
