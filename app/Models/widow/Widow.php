<?php

namespace App\Models\widow;

use App\Models\WidowFile;
use Illuminate\Database\Eloquent\Model;

class Widow extends Model
{
    protected $fillable = ['name', 'tel', 'cin', 'created_by_admin','birth_date','is_supported'];


    function orphans()
    {
        return $this->hasMany(Orphan::class, 'widow_id');
    }

    public function files()
    {
        return $this->hasMany(WidowFile::class);
    }



}
