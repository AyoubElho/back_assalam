<?php

namespace App\Models;

use App\Models\distitute_tempo\DestituteTempo;
use App\Models\widow_tempo\WidowTempo;
use Illuminate\Database\Eloquent\Model;

class RequestModel extends Model
{
    protected $table = 'requests';

    protected $fillable = [
        'application_type',
        'submission_date',
        'status',
        'user_id',
        ];

    function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    function requestFiles()
    {
        return $this->hasMany(RequestFile::class, 'request_id');
    }

    function widow()
    {
        return $this->hasOne(WidowTempo::class, 'request_id'); // Correct the foreign key
    }

    function destitute()
    {
        return $this->hasOne(DestituteTempo::class, 'request_id');
    }



}
