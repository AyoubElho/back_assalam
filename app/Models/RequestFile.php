<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestFile extends Model
{
    use HasFactory;

    protected $table = 'request_files';
    protected $fillable = ['request_id', 'file_type', 'file_path','status','note_admin'];

    function request()
    {
        return $this->belongsTo(RequestModel::class, 'request_id');
    }
}
