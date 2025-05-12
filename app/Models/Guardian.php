<?php

namespace App\Models;

use App\Models\widow\Orphan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{

    use HasFactory;

    protected $table = 'guardians';
    protected $fillable = ['name', 'email', 'telephone', 'pic_guardian'];


    function orphans()
    {
        return $this->hasMany(Orphan::class);
    }
}
