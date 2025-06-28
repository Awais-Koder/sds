<?php

namespace App\Http\Controllers;

use App\Models\Submittel;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class SubmittelFilesController extends Controller
{
    public function downloadSubmittelFiles($id)
    {
        $submittel = Submittel::with('outgoings')->findOrFail($id);

        $zipFileName = 'submittel-files-' . $submittel->ref_no . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($submittel->outgoings as $outgoing) {
                $fileName = $outgoing->file; // e.g. "01JYR383T0NJG836R7MVRZNPXF.pdf"
                $filePath = Storage::disk('public')->path($fileName);

                if (file_exists($filePath)) {
                    // Optional: Give readable name inside zip
                    $zip->addFile($filePath, basename($fileName));
                }
            }
            $zip->close();

            return response()->download($zipFilePath);
        } else {
            return response()->json(['error' => 'Could not create ZIP file.'], 500);
        }
    }
}
