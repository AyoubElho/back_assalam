<?php

namespace App\Http\Controllers;

use App\Models\widow\Orphan;

class OrphaneController extends Controller
{

    function getAllOrphanes()
    {
        return Orphan::all()->load('widow');
    }

    function count()
    {
        return Orphan::all()->count();
    }
}
