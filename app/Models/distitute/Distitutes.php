<?php

namespace App\Models\distitute;
use Illuminate\Database\Eloquent\Model;

class Distitutes extends Model
{
    protected $fillable = ['name', 'cin', 'tel', 'birth_date', 'husband_id'];

    public function husband()
    {
        return $this->belongsTo(Husband::class);
    }

    public function files()
    {
        return $this->hasMany(FileDistitute::class, 'distitute_id');
    }
}
