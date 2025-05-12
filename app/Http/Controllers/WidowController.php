<?php

namespace App\Http\Controllers;

use App\Models\widow\Orphan;
use App\Models\widow\Widow;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WidowController extends Controller
{
    public function store(Request $request)
    {
        // Validate the input data
        $request->validate([
            'name' => 'required|string|max:255',
            'tel' => 'required|string|max:20',
            'cin' => 'required|string|max:20',
            'orphans' => 'required|array|min:1',
            'orphans.*.full_name' => 'required|string|max:255',
            'orphans.*.birth_date' => 'required|date',
            'orphans.*.is_studying' => 'required|boolean',
        ]);

        // Create the widow
        $widow = Widow::create([
            'name' => $request->name,
            'tel' => $request->tel,
            'cin' => $request->cin,
            'birth_date' => $request->birth_date,
            'created_by_admin' => Auth::user()->id,
        ]);

        // Loop through the orphans and associate them with the widow
        foreach ($request->orphans as $orphanData) {
            Orphan::create([
                'full_name' => $orphanData['full_name'],
                'birth_date' => $orphanData['birth_date'],
                'is_studying' => $orphanData['is_studying'],
                'widow_id' => $widow->id,
            ]);
        }

        return response()->json([
            'message' => 'Widow and orphans stored successfully.',
            'data' => $widow->load('orphans'),
        ], 201);
    }

    public function getAll()
    {
        $widows = Widow::all()->load('orphans','files');

        // Iterate through each widow and check their orphans' studying status
        foreach ($widows as $widow) {
            $hasStudyingOrphan = $widow->orphans->contains(function ($orphan) {
                return $orphan->is_studying;
            });

            // Update the widow's support status if no orphan is studying
            if (!$hasStudyingOrphan) {
                $widow->is_supported = 0;
                $widow->save();
            } else {
                $widow->is_supported = 1;
                $widow->save();
            }
        }

        return response()->json([
            'data' => $widows
        ]);
    }

    public function countWidows()
    {
        $totalWidows = Widow::sum('is_supported');
        return response()->json([
            'total' => $totalWidows
        ]);

    }


    public function update(Request $request, $id)
    {
        $widow = Widow::find($id);
        if (!$widow) {
            return response()->json(['message' => 'Widow not found'], 404);
        }

        // Parse the date received from the frontend
        $birthDate = Carbon::parse($request->birth_date);

        // Update the widow's details
        $widow->update([
            'name' => $request->name,
            'tel' => $request->tel,
            'cin' => $request->cin,
            'birth_date' => $birthDate, // Set the parsed date
            'is_supported' => $request->is_supported
        ]);

        return response()->json([
            'message' => 'Widow updated successfully.',
            'data' => $widow
        ], 200);
    }


    public function destroy($id)
    {
        // Find the widow by ID
        $widow = Widow::find($id);

        // If the widow is not found, return an error response
        if (!$widow) {
            return response()->json(['message' => 'Widow not found'], 404);
        }

        // Delete associated orphans first to maintain referential integrity
        $widow->orphans()->delete();

        // Then delete the widow
        $widow->delete();

        // Return a success message
        return response()->json([
            'message' => 'Widow and associated orphans deleted successfully.'
        ], 200);
    }



}
