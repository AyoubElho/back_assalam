<?php

namespace App\Http\Controllers;

use App\Models\distitute\Distitutes;
use App\Models\distitute\Husband;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DistituteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tel' => 'required|string|max:20',
            'cin' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'husband' => 'required|array',
            'husband.name' => 'required|string|max:255',
            'husband.cin' => 'required|string|max:20',
            'husband.tel' => 'required|string|max:20',
            'husband.birth_date' => 'required|date',
        ]);

        // ✅ Copier les données validées et modifier les dates
        $validated = $request->all();
        $validated['birth_date'] = Carbon::parse($validated['birth_date'])->format('Y-m-d');
        $validated['husband']['birth_date'] = Carbon::parse($validated['husband']['birth_date'])->format('Y-m-d');

        // ✅ Créer le mari
        $husband = Husband::create([
            'name' => $validated['husband']['name'],
            'cin' => $validated['husband']['cin'],
            'tel' => $validated['husband']['tel'],
            'birth_date' => $validated['husband']['birth_date'],
        ]);

        // ✅ Créer la personne nécessiteuse
        $distitute = Distitutes::create([
            'name' => $validated['name'],
            'cin' => $validated['cin'],
            'tel' => $validated['tel'],
            'birth_date' => $validated['birth_date'],
            'husband_id' => $husband->id,
        ]);

        return response()->json([
            'distitute' => $distitute->load('husband')
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'tel' => 'sometimes|string|max:20',
            'cin' => 'sometimes|string|max:20',
            'birth_date' => 'sometimes|date',
            'husband' => 'sometimes|array',
            'husband.name' => 'sometimes|string|max:255',
            'husband.cin' => 'sometimes|string|max:20',
            'husband.tel' => 'sometimes|string|max:20',
            'husband.birth_date' => 'sometimes|date',
        ]);

        // Find the distitute record
        $distitute = Distitutes::findOrFail($id);

        // Update distitute fields if they exist in the request
        $distitute->update([
            'name' => $request->input('name', $distitute->name),
            'tel' => $request->input('tel', $distitute->tel),
            'cin' => $request->input('cin', $distitute->cin),
            'birth_date' => $request->input('birth_date', $distitute->birth_date),
        ]);

        // If husband data is provided, update the husband record
        if ($request->has('husband')) {
            $husband = $distitute->husband;
            $husband->update([
                'name' => $request->input('husband.name', $husband->name),
                'cin' => $request->input('husband.cin', $husband->cin),
                'tel' => $request->input('husband.tel', $husband->tel),
                'birth_date' => $request->input('husband.birth_date', $husband->birth_date),
            ]);
        }

        // Return the updated distitute with husband relationship loaded
        return response()->json([
            'distitute' => $distitute->load('husband')
        ]);
    }


    public function showAll()
    {
        $distitutes = Distitutes::with(['husband','files'])->get();

        return response()->json([
            'distitutes' => $distitutes
        ]);
    }

    public function destroy($id)
    {
        // Find the distitute record
        $distitute = Distitutes::findOrFail($id);

        // Optionally, delete the related husband record if it's no longer needed
        $husband = $distitute->husband;
        if ($husband) {
            $husband->delete();
        }

        // Delete the distitute record
        $distitute->delete();

        // Return success response
        return response()->json([
            'message' => 'Distitute and associated husband record deleted successfully.'
        ], 200);
    }

}
