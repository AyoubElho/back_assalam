<?php

namespace App\Http\Controllers;

use App\Models\RequestFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequestFileController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request.
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',  // Ensures request_id exists in the requests table.
            'cin' => 'required',
            'file_type' => 'required|in:طلب_الترشيح,البطاقة_الوطنية,بطاقة_الرميد,الحالة_المدنية,عقد_الازدياد,شهادة_الوفاة,شهادة_الحياة,شهادة_حسن_السيرة,شهادة_طبية,عقد_الزواج,شهادة_عدم_الزواج,صورة_شخصية,صورة_عائلية',
            'file_path' => 'required|mimes:pdf,jpeg,png,jpg|max:2048',  // Allow pdf and image files (jpeg, png, jpg).
        ]);

        // Check if a file is uploaded.
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $filename = $validated['file_type'] . '_' . time() . '.' . $file->getClientOriginalExtension();  // Generate unique filename.

            // Store the file in the 'public/uploads' directory.
            $path = $file->storeAs('uploads/users/' . $validated['cin'] . '/' . $validated['file_type'], $filename, 'public');
        } else {
            return response()->json(['error' => 'No file uploaded'], 400);  // Return an error if no file is uploaded.
        }

        // Save the file information in the database.
        $requestFile = RequestFile::create([
            'request_id' => $validated['request_id'],
            'file_type' => $validated['file_type'],
            'file_path' => $path,
        ]);

        // Return a successful response.
        return response()->json([
            'message' => 'File uploaded successfully!',
            'data' => $requestFile
        ], 201);
    }

    public function updateFileStatus($fileId, $status)
    {
        $file = RequestFile::find($fileId);

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $file->status = $status;
        $file->save();

        return response()->json(['message' => 'File status updated successfully'], 200);
    }

    public function reuploadFile(Request $request, $fileId)
    {
        // Validate the incoming request for the new file.
        $validated = $request->validate([
            'file_path' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_type' => 'required|in:طلب_الترشيح,البطاقة_الوطنية,بطاقة_الرميد,الحالة_المدنية,عقد_الازدياد,شهادة_الوفاة,شهادة_الحياة,شهادة_حسن_السيرة,شهادة_طبية,عقد_الزواج,شهادة_عدم_الزواج,صورة_شخصية,صورة_عائلية',
        ]);

        // Find the file record to update
        $file = RequestFile::find($fileId);

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        // Load related request and application type
        $file->load('request');

        $cin = ''; // Variable to hold CIN

        if ($file->request->application_type === 'يتيم_أرملة') {
            $file->load('request.widow');
            $cin = $file->request->widow->cin;
        } elseif ($file->request->application_type === 'أسرة_معوزة') {
            $file->load('request.destitute');
            $cin = $file->request->destitute->cin;
        }

        // Delete old file
        if (Storage::exists($file->file_path)) {
            Storage::delete($file->file_path);
        }

        // Store new file
        if ($request->hasFile('file_path')) {
            $newFile = $request->file('file_path');
            $filename = $validated['file_type'] . '_' . time() . '.' . $newFile->getClientOriginalExtension();

            $newFilePath = $newFile->storeAs(
                'uploads/users/' . $file->request->application_type . '/' . $cin . '/' . $validated['file_type'],
                $filename,
                'public'
            );

            // Update file model
            $file->file_path = $newFilePath;
            $file->status = 'pending';
            $file->save();

            // ✅ Now update the parent request status
            $relatedRequest = $file->request;  // This is your Request model instance
            $relatedRequest->status = 'تمت_إعادة_رفع_الملفات';
            $relatedRequest->save();

            return response()->json([
                'message' => 'File re-uploaded and status updated successfully!',
                'data' => $file
            ], 200);

        } else {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    }

    public function addNote($fileId, $note)
    {
        $file = RequestFile::find($fileId);

        if (!$file) {
            return response()->json([
                'message' => 'Request file not found.',
            ], 404);
        }

        $file->note_admin = $note;
        $file->save();

        return response()->json([
            'message' => 'Note added successfully!',
        ]);
    }


}
