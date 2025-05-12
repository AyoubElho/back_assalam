<?php

namespace App\Models\distitute;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileDistitute extends Model
{
    use HasFactory;

    protected $table = 'file_distitutes'; // optional if the table name matches Laravel's pluralization rules

    protected $fillable = [
        'distitute_id',
        'file_type',
        'file_path',
        'status',
        'note_admin',
    ];


    // Optional relationships (if needed)
    public function distitute()
    {
        return $this->belongsTo(Distitutes::class);
    }
}
