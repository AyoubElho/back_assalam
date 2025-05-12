<?php

namespace App\Models;

use App\Models\widow\Widow;
use Illuminate\Database\Eloquent\Model;

class WidowFile extends Model
{
    protected $fillable = [
        'widow_id',
        'file_type',
        'file_path',
        'status',
        'note_admin',
    ];

    public function widow()
    {
        return $this->belongsTo(Widow::class);
    }
}
