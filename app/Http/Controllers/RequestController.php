<?php

namespace App\Http\Controllers;

use App\Models\distitute\FileDistitute;
use App\Models\distitute_tempo\DestituteTempo;
use App\Models\distitute_tempo\HusbandTempo;
use App\Models\RequestFile;
use App\Models\RequestModel;
use App\Models\widow\Orphan;
use App\Models\widow\Widow;
use App\Models\widow_tempo\OrphanTempo;
use App\Models\widow_tempo\WidowTempo;
use App\Models\WidowFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Add the OrphanTempo model

class RequestController extends Controller
{
    public function store(Request $request)
    {
// Validate the incoming request
        $validated = $request->validate([
            'application_type' => 'required|in:يتيم_أرملة,أسرة_معوزة',
            'list_files' => 'required|array|min:1',
            'list_files.*.file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'list_files.*.file_type' => 'required|in:طلب_الترشيح,البطاقة_الوطنية,بطاقة_الرميد,الحالة_المدنية,عقد_الازدياد,شهادة_الوفاة,شهادة_الحياة,شهادة_حسن_السيرة,شهادة_طبية,عقد_الزواج,شهادة_عدم_الزواج,صورة_شخصية,صورة_عائلية',

            // Widow (يتيم_أرملة) fields
            'widow' => 'required_if:application_type,يتيم_أرملة|array',
            'widow.name' => 'required_if:application_type,يتيم_أرملة|string',
            'widow.tel' => 'required_if:application_type,يتيم_أرملة|string',
            'widow.cin' => 'required_if:application_type,يتيم_أرملة|string',
            'widow.birth_date' => 'required_if:application_type,يتيم_أرملة|date',
            'widow.orphans' => 'required_if:application_type,يتيم_أرملة|array',
            'widow.orphans.*.name' => 'required_if:application_type,يتيم_أرملة|string',
            'widow.orphans.*.birth_date' => 'required_if:application_type,يتيم_أرملة|date',
            'widow.orphans.*.is_studying' => 'required_if:application_type,يتيم_أرملة|boolean',

            // Distitutes (أسرة_معوزة) fields
            'name' => 'required_if:application_type,أسرة_معوزة|string',
            'cin' => 'required_if:application_type,أسرة_معوزة|string',
            'phone' => 'required_if:application_type,أسرة_معوزة|string',
            'birth_date' => 'required_if:application_type,أسرة_معوزة|date',
            'husband' => 'required_if:application_type,أسرة_معوزة|array',
            'husband.name' => 'required_if:application_type,أسرة_معوزة|string',
            'husband.cin' => 'required_if:application_type,أسرة_معوزة|string',
            'husband.phone' => 'required_if:application_type,أسرة_معوزة|string',
            'husband.birth_date' => 'required_if:application_type,أسرة_معوزة|date',
        ]);

        $requestRecord = RequestModel::create([
            'application_type' => $validated['application_type'],
            'status' => 'قيد_المراجعة',
            'user_id' => auth()->id(),
            'submission_date' => now(),
        ]);

        if ($validated['application_type'] === 'يتيم_أرملة') {
            $widow = WidowTempo::create([
                'name' => $validated['widow']['name'],
                'tel' => $validated['widow']['tel'],
                'cin' => $validated['widow']['cin'],
                'birth_date' => $validated['widow']['birth_date'],
                'request_id' => $requestRecord->id,
            ]);

            foreach ($validated['widow']['orphans'] as $orphan) {
                OrphanTempo::create([
                    'full_name' => $orphan['name'],
                    'birth_date' => $orphan['birth_date'],
                    'is_studying' => $orphan['is_studying'],
                    'widow_id' => $widow->id,
                ]);
            }
        }

        if ($validated['application_type'] === 'أسرة_معوزة') {
            $husband = HusbandTempo::create([
                'name' => $validated['husband']['name'],
                'phone' => $validated['husband']['phone'],
                'cin' => $validated['husband']['cin'],
                'birth_date' => $validated['husband']['birth_date'],
            ]);

            DestituteTempo::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'cin' => $validated['cin'],
                'birth_date' => $validated['birth_date'],
                'husband_id' => $husband->id,
                'request_id' => $requestRecord->id,
            ]);
        }

// Handle file uploads
        foreach ($validated['list_files'] as $fileItem) {
            $uploadedFile = $fileItem['file'];
            $fileType = $fileItem['file_type'];
            $filename = $fileType . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();

            $cinFolder = $validated['application_type'] === 'يتيم_أرملة'
                ? $validated['widow']['cin']
                : $validated['cin'];

            $path = $uploadedFile->storeAs(
                'uploads/users/' . $requestRecord['application_type'] . '/' . $cinFolder . '/' . $fileType,
                $filename,
                'public'
            );

            RequestFile::create([
                'request_id' => $requestRecord->id,
                'file_type' => $fileType,
                'file_path' => $path,
            ]);
        }

        return response()->json([
            'message' => 'تم تقديم الطلب بنجاح',
            'data' => $requestRecord
        ], 201);
    }

    public function findById($id)
    {
        if (RequestModel::where('id', $id)->value('application_type') == 'يتيم_أرملة') {
            $request = RequestModel::with('widow.orphans', 'requestFiles')->findOrFail($id);
        } else {
            $request = RequestModel::with('requestFiles', 'destitute.husband')->findOrFail($id);
        }

        return response()->json($request);
    }


    public function updateStatus($idRequest, $status)
    {
        try {
            $twilio = new SmsController();
            $emailController = new StatusMailController();
            $phone = '';

            $applicationType = RequestModel::where('id', $idRequest)->value('application_type');
            if (!$applicationType) {
                return response()->json(['error' => 'Request not found'], 404);
            }

            if ($applicationType === 'يتيم_أرملة') {
                $request = RequestModel::with('widow.orphans', 'requestFiles', 'user')->findOrFail($idRequest);
                if (!$request->widow) return response()->json(['error' => 'Missing widow info'], 404);
                $phone = $request->widow->tel;
            } else {
                $request = RequestModel::with('requestFiles', 'destitute.husband', 'user')->findOrFail($idRequest);
                if (!$request->destitute) return response()->json(['error' => 'Missing destitute info'], 404);
                $phone = $request->destitute->phone;
            }

            $updated = $request->update(['status' => $status]);

            $messages = [
                'قيد_مراجعة_المسؤول' => 'طلبك كامل، وهو الآن قيد مراجعة المسؤول',
                'غير_مكتمل' => 'طلبك غير مكتمل...',
                'مقبول' => 'تهانينا! تم قبول طلبك.',
                'مرفوض' => 'نأسف، تم رفض طلبك.',
                'قيد_الانتظار' => 'طلبك في مرحلة الانتظار...',
                'قيد_التأكيد' => 'الطلب شبه مقبول، يرجى إحضار الورقة الرسمية للتأكيد'
            ];


            if ($status === 'مقبول' && $applicationType === 'يتيم_أرملة') {
                $existing = Widow::where('cin', $request->widow->cin)->first();

                if (!$existing) {
                    $newWidow = Widow::create([
                        'name' => $request->widow->name,
                        'tel' => $request->widow->tel,
                        'cin' => $request->widow->cin,
                        'birth_date' => $request->widow->birth_date,
                        'created_by_admin' => auth()->id(),
                    ]);

                    foreach ($request->widow->orphans as $orphan) {
                        Orphan::create([
                            'full_name' => $orphan->full_name,
                            'birth_date' => $orphan->birth_date,
                            'is_studying' => $orphan->is_studying,
                            'widow_id' => $newWidow->id,
                        ]);
                    }

                    foreach ($request->requestFiles as $file) {
                        WidowFile::create([
                            'widow_id' => $newWidow->id,
                            'file_type' => $file->file_type,
                            'file_path' => $file->file_path,
                            'status' => $file->status,
                            'note_admin' => $file->note_admin,
                        ]);
                    }
                }
            }


            if ($status === 'مقبول' && $applicationType === 'أسرة_معوزة') {
                $existing = \App\Models\distitute\Distitutes::where('cin', $request->destitute->cin)->first();

                if (!$existing) {
                    $husband = \App\Models\distitute\Husband::create([
                        'name' => $request->destitute->husband->name,
                        'cin' => $request->destitute->husband->cin,
                        'tel' => $request->destitute->husband->phone,
                        'birth_date' => $request->destitute->husband->birth_date,
                    ]);

                    $destitute = \App\Models\distitute\Distitutes::create([
                        'name' => $request->destitute->name,
                        'cin' => $request->destitute->cin,
                        'tel' => $request->destitute->phone,
                        'birth_date' => $request->destitute->birth_date,
                        'husband_id' => $husband->id,
                        'created_by_admin' => auth()->id(),
                    ]);

                    foreach ($request->requestFiles as $file) {
                        FileDistitute::create([
                            'distitute_id' => $destitute->id,
                            'file_type' => $file->file_type,
                            'file_path' => $file->file_path,
                            'status' => $file->status,
                            'note_admin' => $file->note_admin,
                        ]);
                    }
                }
            }


            if ($updated && isset($messages[$status])) {
                // $twilio->sendSms($phone, $messages[$status]);
                // if ($request->user && isset($request->user->email)) {
                //     $emailController->sendEmailToUser($request->user, $messages, $status);
                // }
                return response()->json(['message' => 'Status updated, widow data stored, notifications sent'], 200);
            }

            return response()->json(['error' => 'Failed to update status'], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function getAllRequests()
    {
        // Get requests of type 'يتيم_أرملة'
        $widowRequests = RequestModel::with('widow.orphans', 'requestFiles')
            ->where('application_type', 'يتيم_أرملة')
            ->get();
        // Get other requests
        $destituteRequests = RequestModel::with('destitute.husband', 'requestFiles')
            ->where('application_type', '!=', 'يتيم_أرملة')
            ->get();
        // Merge both collections
        $allRequests = $widowRequests->merge($destituteRequests);

        return response()->json($allRequests);
    }


    public
    function findByStatus($status)
    {
        // Find requests by status with associated files, widow, and orphans
        $requests = RequestModel::with('widow.orphans', 'requestFiles')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }


    public
    function countAllRequests($status)
    {
        return RequestModel::all()->where('status', $status)->count();
    }
}
