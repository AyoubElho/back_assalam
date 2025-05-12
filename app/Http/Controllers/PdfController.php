<?php

namespace App\Http\Controllers;

use App\Models\RequestModel;
use Mpdf\Mpdf;
use Illuminate\Support\Carbon;

class PdfController extends Controller
{
    public function downloadPDF($requestId)
    {
        if (RequestModel::where('id', $requestId)->value('application_type') == 'يتيم_أرملة') {
            $request = RequestModel::with('widow.orphans', 'requestFiles')->findOrFail($requestId);
        } else {
            $request = RequestModel::with('requestFiles', 'destitute.husband')->findOrFail($requestId);
        }

        $date = Carbon::now()->format('Y-m-d');

        $html = view('pdf.myPDF', compact("date", "request"))->render();

        $mpdf = new Mpdf([
            'default_font' => 'dejavusans', // built-in font that supports Arabic
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'directionality' => 'rtl',
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="request_info.pdf"',
        ]);
    }
}
