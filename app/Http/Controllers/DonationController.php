<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    function getAll()
    {
        return Donation::with('user')->get();
    }

    function totalAmount()
    {
        // Calculate the total amount of donations
        $totalAmount = Donation::sum('amount');
        return response()->json(['total_amount' => $totalAmount]);
    }
}
