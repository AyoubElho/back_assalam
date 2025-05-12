<?php

namespace App\Models\distitute_tempo;

use App\Models\RequestModel;
use Illuminate\Database\Eloquent\Model;

class DestituteTempo extends Model
{
    protected $fillable = ['name', 'phone', 'cin', 'husband_id', 'request_id','birth_date'];
    protected $table = 'destitutes_tempos';

    public function husband()
    {
        // correct: belongsTo, foreign key on destitutes_tempos
        return $this->belongsTo(HusbandTempo::class, 'husband_id');
    }


    function request()
    {
        return $this->belongsTo(RequestModel::class, 'request_id');
    }
}
