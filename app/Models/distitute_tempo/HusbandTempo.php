<?php

namespace App\Models\distitute_tempo;

use Illuminate\Database\Eloquent\Model;

class HusbandTempo extends Model
{
    protected $fillable = ['name', 'phone', 'cin','birth_date'];

    public function destitute()
    {
        return $this->hasOne(DestituteTempo::class, 'husband_id');
    }

}
