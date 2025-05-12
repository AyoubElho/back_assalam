<?php
namespace App\Models\distitute;
use Illuminate\Database\Eloquent\Model;

class Husband extends Model
{
    protected $fillable = ['name', 'cin', 'tel', 'birth_date'];

    public function destitute()
    {
        return $this->hasOne(Distitutes::class);
    }
}
